<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class BackupDatabase extends Command
{
    protected $signature = 'otfk:backup
        {--keep=14 : Скільки останніх копій зберігати (старіші видаляються)}';

    protected $description = 'Резервна копія бази даних (mysqldump + gzip) у storage/app/backups';

    public function handle(): int
    {
        $connection = config('database.default');
        $db = config("database.connections.{$connection}");

        if (($db['driver'] ?? null) !== 'mysql') {
            $this->error("Підтримується лише MySQL. Поточний драйвер: " . ($db['driver'] ?? 'невідомо'));

            return self::FAILURE;
        }

        $dir = storage_path('app/backups');
        File::ensureDirectoryExists($dir);

        $database = $db['database'];
        $stamp = now()->format('Y-m-d_His');
        $sqlFile = "{$dir}/otfk_{$database}_{$stamp}.sql";
        $gzFile = "{$sqlFile}.gz";

        $this->info("Створюю резервну копію бази «{$database}»…");

        // mysqldump пише напряму у файл (--result-file коректно працює і на Windows).
        // Пароль передаємо через змінну середовища MYSQL_PWD, щоб він не світився
        // у списку процесів (ps/таск-менеджер).
        $process = new Process([
            $this->mysqldumpBinary(),
            '--host=' . ($db['host'] ?? '127.0.0.1'),
            '--port=' . ($db['port'] ?? 3306),
            '--user=' . ($db['username'] ?? 'root'),
            '--single-transaction',  // консистентний дамп InnoDB без блокування таблиць
            '--quick',               // потокове читання рядків (менше пам'яті)
            '--no-tablespaces',      // не потребує привілею PROCESS (важливо на шаред-хостингу)
            '--default-character-set=utf8mb4',
            '--result-file=' . $sqlFile,
            $database,
        ]);
        $process->setEnv(['MYSQL_PWD' => (string) ($db['password'] ?? '')]);
        $process->setTimeout(600);

        try {
            $process->mustRun();
        } catch (ProcessFailedException $e) {
            @unlink($sqlFile);
            $this->error('Помилка mysqldump: ' . trim($process->getErrorOutput() ?: $e->getMessage()));
            $this->line('Підказка: переконайтесь, що mysqldump доступний, або вкажіть шлях у .env → MYSQLDUMP_PATH');

            return self::FAILURE;
        }

        if (! File::exists($sqlFile) || File::size($sqlFile) === 0) {
            @unlink($sqlFile);
            $this->error('Дамп не створено (порожній файл).');

            return self::FAILURE;
        }

        // Стискаємо засобами PHP (zlib) — працює однаково на Windows і Linux,
        // не залежить від наявності утиліти gzip у системі.
        $this->gzipFile($sqlFile, $gzFile);
        @unlink($sqlFile);

        $size = File::size($gzFile);
        $this->info('✓ Готово: ' . basename($gzFile) . ' (' . $this->humanSize($size) . ')');

        $this->rotate($dir, (int) $this->option('keep'));

        return self::SUCCESS;
    }

    protected function mysqldumpBinary(): string
    {
        // На хостингу зазвичай у PATH. За потреби — задати повний шлях у .env.
        return env('MYSQLDUMP_PATH', 'mysqldump');
    }

    protected function gzipFile(string $source, string $dest): void
    {
        $in = fopen($source, 'rb');
        $out = gzopen($dest, 'wb9');

        while (! feof($in)) {
            gzwrite($out, fread($in, 524288)); // по 512 КБ
        }

        gzclose($out);
        fclose($in);
    }

    protected function rotate(string $dir, int $keep): void
    {
        if ($keep <= 0) {
            return;
        }

        $files = collect(File::glob($dir . '/otfk_*.sql.gz'))
            ->sortByDesc(fn ($f) => File::lastModified($f))
            ->values();

        foreach ($files->slice($keep) as $old) {
            File::delete($old);
            $this->line('  видалено стару копію: ' . basename($old));
        }
    }

    protected function humanSize(int $bytes): string
    {
        $units = ['Б', 'КБ', 'МБ', 'ГБ'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 1) . ' ' . $units[$i];
    }
}
