<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\MataKuliah;
use App\Models\Material;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Dosen Pengampu Default
        $dosen = User::updateOrCreate(
            ['email' => 'dosen@celoe.test'],
            [
                'name' => 'Dr. Ir. Budi Santoso, M.T.',
                'password' => Hash::make('dosen123'),
                'role' => 'dosen',
                'nomor_induk' => '198501012015041001',
            ]
        );

        // 2. Akun Mahasiswa Default
        $mahasiswa1 = User::updateOrCreate(
            ['email' => 'mahasiswa@celoe.test'],
            [
                'name' => 'Ahmad Pratama',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '1301210001',
            ]
        );

        // 3. Mata Kuliah Dasar (Modul 3)
        $course1 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF301'],
            [
                'nama_mk' => 'Pengembangan Aplikasi Website',
                'deskripsi' => 'Mata kuliah yang membahas arsitektur aplikasi web modern menggunakan framework Laravel, MVC, Eloquent ORM, dan keamanan web.',
                'sks' => 4,
                'semester' => 'Ganjil 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        // 4. Daftarkan Mahasiswa ke Mata Kuliah Pertama (status approved)
        $course1->students()->syncWithoutDetaching([
            $mahasiswa1->id => ['status' => 'approved'],
        ]);

        // 5. Tambahkan Materi Contoh
        Material::updateOrCreate(
            ['course_id' => $course1->id, 'minggu_ke' => 1, 'urutan' => 1],
            [
                'judul' => 'Pengenalan Arsitektur MVC & Routing di Laravel 12',
                'konten' => 'Mempelajari alur HTTP Request, Route Middleware, Controller, dan pembuatan View menggunakan Blade Template.',
            ]
        );


        // TODO 8: Tambahkan minimal 2 mata kuliah lagi dengan semester berbeda, 1 mahasiswa tambahan, 2 tugas, dan contoh data submission
        $mahasiswa2 = User::updateOrCreate(
            ['email' => 'siti@celoe.test'],
            [
                'name' => 'Siti Rahmawati',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '1301210002',
            ]
        );

        $course2 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF204'],
            [
                'nama_mk' => 'Basis Data Lanjut',
                'deskripsi' => 'Pembahasan normalisasi, optimasi query, indexing, transaksi, dan perancangan basis data relasional.',
                'sks' => 3,
                'semester' => 'Ganjil 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        $course3 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF402'],
            [
                'nama_mk' => 'Keamanan Aplikasi Web',
                'deskripsi' => 'Mempelajari OWASP Top 10, autentikasi, otorisasi, serta praktik pengamanan aplikasi web.',
                'sks' => 2,
                'semester' => 'Genap 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        $course1->students()->syncWithoutDetaching([
            $mahasiswa2->id => ['status' => 'approved'],
        ]);
        $course2->students()->syncWithoutDetaching([
            $mahasiswa1->id => ['status' => 'approved'],
            $mahasiswa2->id => ['status' => 'approved'],
        ]);

        $assignment1 = Assignment::updateOrCreate(
            ['course_id' => $course1->id, 'judul' => 'Implementasi Middleware Role di Laravel'],
            [
                'deskripsi' => 'Jelaskan cara kerja middleware auth dan role pada Laravel, lalu berikan contoh penerapannya pada route dosen dan mahasiswa.',
                'deadline' => now()->addDays(14),
            ]
        );

        $assignment2 = Assignment::updateOrCreate(
            ['course_id' => $course2->id, 'judul' => 'Normalisasi Basis Data'],
            [
                'deskripsi' => 'Lakukan normalisasi tabel transaksi penjualan sampai bentuk normal ketiga (3NF) dan jelaskan setiap tahapannya.',
                'deadline' => now()->addDays(21),
            ]
        );

        Submission::updateOrCreate(
            ['assignment_id' => $assignment1->id, 'user_id' => $mahasiswa1->id],
            [
                'jawaban' => 'Middleware auth memastikan hanya pengguna yang sudah login yang dapat mengakses route tertentu, sedangkan middleware role memeriksa peran pengguna sebelum request diteruskan ke controller.',
                'nilai' => 88,
                'catatan_dosen' => 'Penjelasan sudah jelas dan contoh penerapan tepat.',
                'submitted_at' => now()->subDays(1),
            ]
        );

        Submission::updateOrCreate(
            ['assignment_id' => $assignment1->id, 'user_id' => $mahasiswa2->id],
            [
                'jawaban' => 'Middleware adalah lapisan yang menyaring HTTP request sebelum masuk ke controller, misalnya untuk cek login dan cek peran pengguna.',
                'nilai' => null,
                'catatan_dosen' => null,
                'submitted_at' => now()->subHours(5),
            ]
        );

        Submission::updateOrCreate(
            ['assignment_id' => $assignment2->id, 'user_id' => $mahasiswa1->id],
            [
                'jawaban' => 'Tabel transaksi dipecah menjadi tabel pelanggan, produk, transaksi, dan detail_transaksi untuk menghilangkan ketergantungan parsial dan transitif.',
                'nilai' => 65,
                'catatan_dosen' => 'Lengkapi penjelasan tahapan 2NF dan 3NF.',
                'submitted_at' => now()->subHours(8),
            ]
        );
    }
}
