<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Cabang;
use App\Models\Pemasok;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Data Cabang Sampel
        $cabangUtama = Cabang::create([
            'nama_cabang' => 'Kantor Pusat Pamulang',
            'alamat' => 'Jl. Surya Kencana No.1, Pamulang, Tangerang Selatan',
        ]);

        $cabangBsd = Cabang::create([
            'nama_cabang' => 'Cabang BSD City',
            'alamat' => 'Ruko BSD Sektor 7, Serpong, Tangerang',
        ]);

        // 2. Buat Data Pemasok Sampel
        Pemasok::create([
            'nama_pemasok' => 'PT. Sinar Sosro Distributor',
            'kontak' => '021-7401234',
        ]);

        Pemasok::create([
            'nama_pemasok' => 'CV. Indofood Makmur Mandiri',
            'kontak' => '0812-9988-7766',
        ]);

        // 3. Buat Akun Admin (Terikat ke Kantor Pusat)
        User::create([
            'name' => 'Fredy',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin@123'),
            'role' => 'admin',
            'telepon' => '081122334455',
            'cabang_id' => $cabangUtama->id,
        ]);

        // 4. Buat Akun Kasir (Ditugaskan di Cabang BSD)
        User::create([
            'name' => 'Adit',
            'email' => 'kasir@gmail.com',
            'password' => Hash::make('kasir@456'),
            'role' => 'kasir',
            'telepon' => '085566778899',
            'cabang_id' => $cabangBsd->id,
        ]);
    }
}