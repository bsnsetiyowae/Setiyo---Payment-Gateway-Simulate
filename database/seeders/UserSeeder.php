<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Setiyo',
            'email' => 'setiyo10@gmail.com',
            'password' => Hash::make('password'), // Ganti dengan password default
        ]);

        $userId = $user->id; // ID user yang ingin Anda buatkan token (sesuaikan dengan ID di tabel users)
        $base64EncodedToken = base64_encode('Setiyo');

        DB::table('personal_access_tokens')->insert([
            'tokenable_type' => 'App\\Models\\User', // Model user
            'tokenable_id' => $userId,
            'name' => 'default',
            'token' => hash('sha256', $base64EncodedToken), // Hash token sesuai kebutuhan Laravel Sanctum
            'abilities' => json_encode(['*']), // Hak akses penuh
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    }
}
