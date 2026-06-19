<?php

namespace App\Models\Concerns;

use App\Support\ImageOptimizer;

trait OptimizesUploadedImages
{
    public static function bootOptimizesUploadedImages(): void
    {
        static::saved(function (self $model): void {
            // Які поля змінились — рахуємо одразу (wasChanged/getOriginal валідні лише тут).
            $tasks = [];
            foreach (static::optimizedImageFields() as $field) {
                if (! $model->wasChanged($field)) {
                    continue;
                }

                $old = $model->getOriginal($field);
                $new = $model->{$field};

                $tasks[] = [
                    'delete' => (filled($old) && $old !== $new) ? $old : null,
                    'webp' => filled($new) ? $new : null,
                ];
            }

            if (! $tasks) {
                return;
            }

            // Важку конвертацію (GD) виносимо ПІСЛЯ віддачі відповіді: вона не блокує
            // збереження і — головне — не ламає його при збої. Воркер не потрібен.
            dispatch(function () use ($tasks): void {
                foreach ($tasks as $task) {
                    try {
                        if ($task['delete']) {
                            ImageOptimizer::deleteVariants($task['delete']);
                        }
                        if ($task['webp']) {
                            ImageOptimizer::toWebp($task['webp']);
                        }
                    } catch (\Throwable $e) {
                        report($e); // оптимізація — допоміжна, не має ламати контент
                    }
                }
            })->afterResponse();
        });

        static::deleted(function (self $model): void {
            foreach (static::optimizedImageFields() as $field) {
                if (filled($model->{$field})) {
                    try {
                        ImageOptimizer::deleteVariants($model->{$field});
                    } catch (\Throwable $e) {
                        report($e);
                    }
                }
            }
        });
    }

    /** @return list<string> */
    protected static function optimizedImageFields(): array
    {
        return static::$optimizedImages ?? [];
    }
}