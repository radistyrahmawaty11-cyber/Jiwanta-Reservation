<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use App\Models\Ulasan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UlasanSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['nama' => 'Andi Saputra', 'kategori' => Ulasan::KATEGORI_TIKET, 'rating' => 1, 'komentar' => 'Kolam terasa kurang bersih saat saya datang, banyak daun dan area bilas licin.'],
            ['nama' => 'Bella Anggraini', 'kategori' => Ulasan::KATEGORI_TIKET, 'rating' => 1, 'komentar' => 'Pelayanan lambat dan petugas tidak ramah saat saya menanyakan lokasi kolam.'],
            ['nama' => 'Candra Wijaya', 'kategori' => Ulasan::KATEGORI_CABIN, 'rating' => 1, 'komentar' => 'Cabin berdebu, AC kurang dingin, dan handuk tidak tersedia saat check-in.'],
            ['nama' => 'Dinda Permata', 'kategori' => Ulasan::KATEGORI_TIKET, 'rating' => 1, 'komentar' => 'Air kolam terlalu dingin padahal disebut kolam hangat, anak saya sampai kedinginan.'],
            ['nama' => 'Eko Saputra', 'kategori' => Ulasan::KATEGORI_CABIN, 'rating' => 1, 'komentar' => 'Kamar cabin bau lembap dan suara dari luar sangat mengganggu saat malam.'],
            ['nama' => 'Fitri Handayani', 'kategori' => Ulasan::KATEGORI_CABIN, 'rating' => 1, 'komentar' => 'Fasilitas banyak yang rusak dan tidak ada informasi perbaikan sama sekali.'],

            ['nama' => 'Galih Ramadhan', 'kategori' => Ulasan::KATEGORI_TIKET, 'rating' => 2, 'komentar' => 'Tempatnya cukup bagus, tetapi antrean tiket terlalu panjang dan tidak teratur.'],
            ['nama' => 'Hana Safitri', 'kategori' => Ulasan::KATEGORI_TIKET, 'rating' => 2, 'komentar' => 'Kolam hangatnya kurang sesuai ekspektasi, air terasa biasa saja.'],
            ['nama' => 'Indra Kusuma', 'kategori' => Ulasan::KATEGORI_CABIN, 'rating' => 2, 'komentar' => 'Cabin sempit dan kebersihan kurang, meskipun pemandangannya lumayan.'],
            ['nama' => 'Jihan Aulia', 'kategori' => Ulasan::KATEGORI_CABIN, 'rating' => 2, 'komentar' => 'Akses menuju cabin cukup jauh dan area sekitarnya kurang rapi.'],
            ['nama' => 'Krisna Bayu', 'kategori' => Ulasan::KATEGORI_CABIN, 'rating' => 2, 'komentar' => 'Kasur cabin kurang nyaman dan air panas sempat mati saat saya menginap.'],
            ['nama' => 'Laras Ayuningtyas', 'kategori' => Ulasan::KATEGORI_TIKET, 'rating' => 2, 'komentar' => 'Harga tiket terasa mahal dibanding fasilitas yang didapat.'],

            ['nama' => 'Muhammad Iqbal', 'kategori' => Ulasan::KATEGORI_TIKET, 'rating' => 3, 'komentar' => 'Pengalaman cukup baik, tetapi area parkir dan akses masuk masih perlu diperbaiki.'],
            ['nama' => 'Nadia Puspita', 'kategori' => Ulasan::KATEGORI_TIKET, 'rating' => 3, 'komentar' => 'Kolamnya nyaman, sayangnya fasilitas bilas kurang lengkap.'],
            ['nama' => 'Oka Mahendra', 'kategori' => Ulasan::KATEGORI_CABIN, 'rating' => 3, 'komentar' => 'Cabin lumayan bersih, tetapi sarapan yang disediakan biasa saja.'],
            ['nama' => 'Putra Aditya', 'kategori' => Ulasan::KATEGORI_CABIN, 'rating' => 3, 'komentar' => 'Suasana tenang dan cocok untuk bersantai, walau pelayanan bisa lebih cepat.'],
            ['nama' => 'Qori Amalia', 'kategori' => Ulasan::KATEGORI_TIKET, 'rating' => 3, 'komentar' => 'Secara keseluruhan standar, tidak buruk tetapi juga belum istimewa.'],
            ['nama' => 'Raka Pratama', 'kategori' => Ulasan::KATEGORI_CABIN, 'rating' => 3, 'komentar' => 'Kamar cabin oke untuk singgah, tetapi kebersihan kamar mandi perlu ditingkatkan.'],

            ['nama' => 'Sinta Maharani', 'kategori' => Ulasan::KATEGORI_TIKET, 'rating' => 4, 'komentar' => 'Kolam hangatnya menyenangkan dan area sekitar bersih, cocok untuk relaksasi.'],
            ['nama' => 'Taufik Hidayat', 'kategori' => Ulasan::KATEGORI_TIKET, 'rating' => 4, 'komentar' => 'Petugas ramah dan proses masuk cukup cepat, pengalaman yang menyenangkan.'],
            ['nama' => 'Umi Kalsum', 'kategori' => Ulasan::KATEGORI_CABIN, 'rating' => 4, 'komentar' => 'Cabin nyaman dengan pemandangan hijau, hanya perlu sedikit perbaikan di kamar mandi.'],
            ['nama' => 'Vina Oktaviani', 'kategori' => Ulasan::KATEGORI_TIKET, 'rating' => 4, 'komentar' => 'Fasilitas tiket lengkap dan anak-anak senang bermain di kolam.'],
            ['nama' => 'Wahyu Firmansyah', 'kategori' => Ulasan::KATEGORI_CABIN, 'rating' => 4, 'komentar' => 'Cabin bersih dan tenang, AC dingin, serta sarapan cukup enak.'],
            ['nama' => 'Yuni Astuti', 'kategori' => Ulasan::KATEGORI_CABIN, 'rating' => 4, 'komentar' => 'Pelayanan baik dan suasana alamnya sangat mendukung untuk liburan keluarga.'],

            ['nama' => 'Zaki Alfarizi', 'kategori' => Ulasan::KATEGORI_TIKET, 'rating' => 5, 'komentar' => 'Kolam hangatnya luar biasa, bersih, dan sangat nyaman untuk berendam sore hari.'],
            ['nama' => 'Ayu Lestari', 'kategori' => Ulasan::KATEGORI_TIKET, 'rating' => 5, 'komentar' => 'Semua petugas ramah dan sigap, pengalaman berkunjung sangat memuaskan.'],
            ['nama' => 'Bagus Setiawan', 'kategori' => Ulasan::KATEGORI_CABIN, 'rating' => 5, 'komentar' => 'Cabin sangat bersih, nyaman, dan pemandangannya membuat liburan terasa sempurna.'],
            ['nama' => 'Citra Dewi', 'kategori' => Ulasan::KATEGORI_TIKET, 'rating' => 5, 'komentar' => 'Fasilitas lengkap, tempat rapi, dan suasana alamnya benar-benar menyegarkan.'],
            ['nama' => 'Dimas Prakoso', 'kategori' => Ulasan::KATEGORI_CABIN, 'rating' => 5, 'komentar' => 'Menginap di cabin sangat menyenangkan, kamar wangi, bersih, dan pelayanan cepat.'],
            ['nama' => 'Elsa Rahmawati', 'kategori' => Ulasan::KATEGORI_CABIN, 'rating' => 5, 'komentar' => 'Tempat terbaik untuk liburan keluarga, anak-anak senang dan orang tua ikut relaksasi.'],
        ];

        foreach ($rows as $index => $row) {
            $pengguna = Pengguna::updateOrCreate(
                ['email' => 'ulasan'.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT).'@jiwanta.test'],
                [
                    'nama_pengguna' => $row['nama'],
                    'nohp' => '08150000'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                    'role' => 'pengunjung',
                    'password' => Hash::make('123456'),
                ]
            );

            Ulasan::updateOrCreate(
                [
                    'pengguna_id' => $pengguna->id,
                    'kategori' => $row['kategori'],
                ],
                [
                    'rating' => $row['rating'],
                    'komentar' => $row['komentar'],
                ]
            );
        }
    }
}
