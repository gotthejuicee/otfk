<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // На проді задайте ADMIN_EMAIL / ADMIN_PASSWORD у .env (репозиторій публічний - не лишайте дефолтний пароль).
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@otfk.od.ua')],
            [
                'name' => 'Адміністратор',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
                'email_verified_at' => now(),
            ]
        );

        $this->call([SiteSeeder::class]);
    }
}
