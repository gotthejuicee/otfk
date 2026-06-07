<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@otfk.od.ua'],
            ['name' => 'Адміністратор', 'password' => Hash::make('password'), 'email_verified_at' => now()]
        );

        $this->call([SiteSeeder::class]);
    }
}
