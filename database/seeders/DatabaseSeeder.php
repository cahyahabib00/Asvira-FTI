<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\KnowledgeBase;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin Habib',
            'email' => 'habib56@gmail.com',
            'password' => Hash::make('uap12345'),
            'email_verified_at' => now(),
        ]);

        // Seed Knowledge Base data
        KnowledgeBase::create([
            'question' => 'Apakah FTI UAP sudah terakreditasi?',
            'answer' => 'Ya, FTI UAP sudah terakreditasi BAN-PT dengan peringkat B untuk semua program studi.',
            'category' => 'Akreditasi',
            'content' => 'Fakultas Teknologi Informasi UAP telah mendapatkan akreditasi B dari BAN-PT untuk semua program studi yang ada.'
        ]);

        KnowledgeBase::create([
            'question' => 'Berapa biaya kuliah per semester?',
            'answer' => 'Biaya kuliah bervariasi tergantung program studi. Untuk informasi detail, silakan hubungi bagian akademik atau kunjungi website resmi UAP.',
            'category' => 'Biaya',
            'content' => 'Biaya kuliah di FTI UAP terdiri dari SPP per semester dan biaya lainnya. Untuk informasi lengkap dapat menghubungi bagian akademik.'
        ]);

        KnowledgeBase::create([
            'question' => 'Apakah tersedia kelas malam atau kelas karyawan?',
            'answer' => 'Ya, FTI UAP menyediakan program kelas karyawan untuk memudahkan mahasiswa yang bekerja. Jadwal kuliah disesuaikan dengan kebutuhan karyawan.',
            'category' => 'Program',
            'content' => 'Program kelas karyawan dirancang khusus untuk mahasiswa yang bekerja dengan jadwal kuliah yang fleksibel.'
        ]);

        KnowledgeBase::create([
            'question' => 'Kapan jadwal pendaftaran dibuka?',
            'answer' => 'Pendaftaran mahasiswa baru biasanya dibuka pada bulan Januari-Februari untuk semester genap dan Juli-Agustus untuk semester ganjil. Untuk informasi terbaru, silakan cek website resmi UAP.',
            'category' => 'Pendaftaran',
            'content' => 'Jadwal pendaftaran mahasiswa baru diatur sesuai dengan kalender akademik UAP.'
        ]);

        KnowledgeBase::create([
            'question' => 'Program studi apa saja yang tersedia di FTI UAP?',
            'answer' => 'FTI UAP menyediakan program studi Teknik Informatika, Sistem Informasi, dan Manajemen Informatika.',
            'category' => 'Program Studi',
            'content' => 'Fakultas Teknologi Informasi UAP memiliki tiga program studi utama yang telah terakreditasi.'
        ]);

        KnowledgeBase::create([
            'question' => 'Bagaimana prosedur pendaftaran mahasiswa baru?',
            'answer' => 'Prosedur pendaftaran dapat dilakukan secara online melalui website resmi UAP atau datang langsung ke kampus. Persyaratan meliputi fotokopi ijazah, SKHUN, dan dokumen pendukung lainnya.',
            'category' => 'Pendaftaran',
            'content' => 'Pendaftaran mahasiswa baru dapat dilakukan secara online maupun offline dengan persyaratan yang telah ditentukan.'
        ]);

        KnowledgeBase::create([
            'question' => 'Apakah ada beasiswa untuk mahasiswa FTI UAP?',
            'answer' => 'Ya, UAP menyediakan berbagai program beasiswa untuk mahasiswa berprestasi dan mahasiswa yang membutuhkan bantuan finansial. Informasi detail dapat diperoleh di bagian kemahasiswaan.',
            'category' => 'Beasiswa',
            'content' => 'Program beasiswa tersedia untuk mendukung mahasiswa dalam menempuh pendidikan di FTI UAP.'
        ]);

        KnowledgeBase::create([
            'question' => 'Bagaimana fasilitas laboratorium di FTI UAP?',
            'answer' => 'FTI UAP dilengkapi dengan laboratorium komputer yang modern, laboratorium jaringan, dan fasilitas praktikum lainnya untuk mendukung kegiatan belajar mengajar.',
            'category' => 'Fasilitas',
            'content' => 'Fasilitas laboratorium di FTI UAP dirancang untuk mendukung praktikum dan penelitian mahasiswa.'
        ]);
    }
}
