<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $akun = [
            [
                'name'   => 'Admin Sekretariat',
                'email'  => 'sekretariat@bpkad.test',
                'bagian' => 'sekretariat',
                'role'   => 'admin',
            ],
            [
                'name'   => 'Staf Anggaran',
                'email'  => 'anggaran@bpkad.test',
                'bagian' => 'anggaran',
                'role'   => 'staf',
            ],
            [
                'name'   => 'Staf Perbendaharaan',
                'email'  => 'perbendaharaan@bpkad.test',
                'bagian' => 'perbendaharaan',
                'role'   => 'staf',
            ],
            [
                'name'   => 'Staf Aset & Akuntansi',
                'email'  => 'aset@bpkad.test',
                'bagian' => 'aset_akuntansi',
                'role'   => 'staf',
            ],
        ];

        foreach ($akun as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                $data + ['password' => 'password123']
            );
        }
    }
}