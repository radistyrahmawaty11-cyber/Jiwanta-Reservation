<?php

namespace Tests\Feature;

use App\Models\Cabin;
use App\Models\Pengguna;
use App\Models\Reservasi;
use App\Models\Tiket;
use App\Models\Transaksi;
use App\Models\Ulasan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UlasanTest extends TestCase
{
    use DatabaseTransactions;

    private function pengguna(string $name, string $role = 'pengunjung'): Pengguna
    {
        $slug = trim(substr(preg_replace('/[^a-z0-9]+/', '-', strtolower($name)), 0, 18), '-');

        return Pengguna::create([
            'nama_pengguna' => 'Test '.ucfirst($role).' '.$name,
            'email' => $slug.'-'.substr(uniqid(), -6).'@jiwanta.test',
            'password' => 'rahasia123',
            'nohp' => '0812'.random_int(10000000, 99999999),
            'role' => $role,
        ]);
    }

    private function transaksi(Pengguna $user, string $kategori, bool $terverifikasi = true): Transaksi
    {
        $transaksi = Transaksi::create([
            'pengguna_id' => $user->id,
            'kode_transaksi' => strtoupper(uniqid('TRX-')),
            'tanggal_transaksi' => now(),
            'total_amount' => 50000,
            'total_bayar' => 50000,
            'status' => 'lunas',
            'verified_at' => $terverifikasi ? now() : null,
        ]);

        $detail = [
            'jumlah' => 1,
            'harga_satuan' => 50000,
            'subtotal' => 50000,
        ];

        if ($kategori === Ulasan::KATEGORI_CABIN) {
            $cabin = Cabin::create([
                'nama_cabin' => 'Cabin Uji '.uniqid(),
                'harga_per_malam' => 750000,
                'kapasitas' => 2,
                'status' => 'tersedia',
            ]);

            $reservasi = Reservasi::create([
                'pengguna_id' => $user->id,
                'cabin_id' => $cabin->id,
                'tanggal_checkin' => now()->toDateString(),
                'tanggal_checkout' => now()->addDay()->toDateString(),
                'jumlah_tamu' => 2,
                'total_harga' => 750000,
                'status' => 'confirmed',
            ]);

            $detail['reservasi_id'] = $reservasi->id;
        } else {
            $tiket = Tiket::create([
                'nama_tiket' => 'Tiket Uji '.uniqid(),
                'harga' => 50000,
                'stok' => 10,
                'kategori' => 'dewasa',
            ]);

            $detail['tiket_id'] = $tiket->id;
        }

        $transaksi->detailtransaksis()->create($detail);

        return $transaksi;
    }

    private function ulasan(Pengguna $user, string $kategori, int $rating, string $komentar): Ulasan
    {
        return Ulasan::create([
            'pengguna_id' => $user->id,
            'kategori' => $kategori,
            'rating' => $rating,
            'komentar' => $komentar,
        ]);
    }

    public function test_pengunjung_tanpa_transaksi_bisa_melihat_ulasan_tanpa_form(): void
    {
        $viewer = $this->pengguna('ulasan-viewer');
        $penulis = $this->pengguna('ulasan-penulis');
        $tiket = $this->ulasan($penulis, Ulasan::KATEGORI_TIKET, 5, 'Ulasan tiket renang yang menyenangkan.');
        $cabin = $this->ulasan($penulis, Ulasan::KATEGORI_CABIN, 4, 'Ulasan cabin yang nyaman dan bersih.');

        $this->actingAs($viewer)
            ->get('/ulasan')
            ->assertOk()
            ->assertSee('Tiket Renang')
            ->assertSee('Cabin')
            ->assertSee('Belum Bisa Menilai Tiket Renang')
            ->assertSee($tiket->komentar)
            ->assertDontSee($cabin->komentar)
            ->assertDontSee('id="ulasan-form"', false);

        $this->actingAs($viewer)
            ->get('/ulasan?kategori=cabin')
            ->assertOk()
            ->assertSee('Belum Bisa Menilai Cabin')
            ->assertSee($cabin->komentar)
            ->assertDontSee($tiket->komentar)
            ->assertDontSee('id="ulasan-form"', false);

        $this->actingAs($viewer)
            ->postJson('/ulasan', [
                'rating' => 5,
                'komentar' => 'Pengunjung tanpa transaksi tidak boleh mengirim ulasan.',
            ])
            ->assertForbidden()
            ->assertJsonPath('ok', false);

        $this->assertDatabaseMissing('ulasans', ['pengguna_id' => $viewer->id]);
    }

    public function test_pengunjung_tiket_terverifikasi_bisa_menilai_tiket_saja(): void
    {
        $user = $this->pengguna('ulasan-tiket');
        $this->transaksi($user, Ulasan::KATEGORI_TIKET);

        $this->actingAs($user)
            ->get('/ulasan')
            ->assertOk()
            ->assertSee('Beri Rating Sekarang')
            ->assertSee('Rating otomatis masuk ke Tiket Renang')
            ->assertSee('id="ulasan-form"', false);

        $this->actingAs($user)
            ->get('/ulasan?kategori=cabin')
            ->assertOk()
            ->assertSee('Rating otomatis masuk ke Tiket Renang')
            ->assertSee('id="ulasan-form"', false);

        $this->actingAs($user)
            ->postJson('/ulasan', [
                'rating' => 5,
                'komentar' => 'Pelayanan tiket renang sangat memuaskan.',
            ])
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('kategori', Ulasan::KATEGORI_TIKET)
            ->assertJsonPath('kategori_dinilai', [Ulasan::KATEGORI_TIKET])
            ->assertJsonPath('data.0.kategori', Ulasan::KATEGORI_TIKET)
            ->assertJsonPath('data.0.rating', 5);

        $this->assertDatabaseHas('ulasans', [
            'pengguna_id' => $user->id,
            'kategori' => Ulasan::KATEGORI_TIKET,
            'rating' => 5,
        ]);
        $this->assertDatabaseMissing('ulasans', [
            'pengguna_id' => $user->id,
            'kategori' => Ulasan::KATEGORI_CABIN,
        ]);
    }

    public function test_pengunjung_cabin_terverifikasi_bisa_menilai_cabin_saja(): void
    {
        $user = $this->pengguna('ulasan-cabin');
        $this->transaksi($user, Ulasan::KATEGORI_CABIN);

        $this->actingAs($user)
            ->get('/ulasan?kategori=cabin')
            ->assertOk()
            ->assertSee('Beri Rating Sekarang')
            ->assertSee('Rating otomatis masuk ke Cabin')
            ->assertSee('id="ulasan-form"', false);

        $this->actingAs($user)
            ->get('/ulasan')
            ->assertOk()
            ->assertSee('Rating otomatis masuk ke Cabin')
            ->assertSee('id="ulasan-form"', false);

        $this->actingAs($user)
            ->postJson('/ulasan', [
                'rating' => 4,
                'komentar' => 'Cabin nyaman dan pemandangan sangat indah.',
            ])
            ->assertOk()
            ->assertJsonPath('kategori', Ulasan::KATEGORI_CABIN)
            ->assertJsonPath('kategori_dinilai', [Ulasan::KATEGORI_CABIN])
            ->assertJsonPath('data.0.rating', 4);

        $this->assertDatabaseHas('ulasans', [
            'pengguna_id' => $user->id,
            'kategori' => Ulasan::KATEGORI_CABIN,
            'rating' => 4,
        ]);
        $this->assertDatabaseMissing('ulasans', [
            'pengguna_id' => $user->id,
            'kategori' => Ulasan::KATEGORI_TIKET,
        ]);
    }

    public function test_transaksi_lunas_belum_diverifikasi_tidak_membuka_rating(): void
    {
        $user = $this->pengguna('ulasan-lunas-belum-verifikasi');
        $this->transaksi($user, Ulasan::KATEGORI_TIKET, false);

        $this->actingAs($user)
            ->get('/ulasan')
            ->assertOk()
            ->assertSee('Belum Bisa Menilai Tiket Renang')
            ->assertDontSee('id="ulasan-form"', false);

        $this->actingAs($user)
            ->postJson('/ulasan', [
                'rating' => 5,
                'komentar' => 'Lunas tanpa verifikasi petugas belum boleh rating.',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('ulasans', ['pengguna_id' => $user->id]);
    }

    public function test_satu_pengunjung_hanya_punya_satu_ulasan_per_kategori(): void
    {
        $user = $this->pengguna('ulasan-dua-kategori');
        $this->transaksi($user, Ulasan::KATEGORI_TIKET);
        $this->transaksi($user, Ulasan::KATEGORI_CABIN);

        $this->actingAs($user)->postJson('/ulasan', [
            'rating' => 5,
            'komentar' => 'Ulasan pertama untuk tiket dan cabin yang cukup panjang.',
        ])
            ->assertOk()
            ->assertJsonPath('kategori', Ulasan::KATEGORI_TIKET)
            ->assertJsonPath('kategori_dinilai', [
                Ulasan::KATEGORI_TIKET,
                Ulasan::KATEGORI_CABIN,
            ]);

        $this->assertSame(2, Ulasan::where('pengguna_id', $user->id)->count());
        $this->assertDatabaseHas('ulasans', [
            'pengguna_id' => $user->id,
            'kategori' => Ulasan::KATEGORI_TIKET,
            'rating' => 5,
        ]);
        $this->assertDatabaseHas('ulasans', [
            'pengguna_id' => $user->id,
            'kategori' => Ulasan::KATEGORI_CABIN,
            'rating' => 5,
        ]);

        $this->actingAs($user)->postJson('/ulasan', [
            'rating' => 4,
            'komentar' => 'Ulasan kedua setelah diperbarui untuk kedua kategori.',
        ])->assertOk();

        $this->assertSame(2, Ulasan::where('pengguna_id', $user->id)->count());
        $this->assertDatabaseHas('ulasans', [
            'pengguna_id' => $user->id,
            'kategori' => Ulasan::KATEGORI_TIKET,
            'rating' => 4,
        ]);
        $this->assertDatabaseHas('ulasans', [
            'pengguna_id' => $user->id,
            'kategori' => Ulasan::KATEGORI_CABIN,
            'rating' => 4,
        ]);
    }

    public function test_ringkasan_dan_daftar_ulasan_terpisah_per_kategori(): void
    {
        $viewer = $this->pengguna('ulasan-ringkasan-viewer');
        $penulisSatu = $this->pengguna('ulasan-ringkasan-satu');
        $penulisDua = $this->pengguna('ulasan-ringkasan-dua');
        $penulisTiga = $this->pengguna('ulasan-ringkasan-tiga');

        $sebelumTiket = Ulasan::ringkasan(Ulasan::KATEGORI_TIKET);
        $sebelumCabin = Ulasan::ringkasan(Ulasan::KATEGORI_CABIN);

        $this->ulasan($penulisSatu, Ulasan::KATEGORI_TIKET, 5, 'Ulasan tiket pertama untuk ringkasan.');
        $this->ulasan($penulisDua, Ulasan::KATEGORI_TIKET, 4, 'Ulasan tiket kedua untuk ringkasan.');
        $cabin = $this->ulasan($penulisTiga, Ulasan::KATEGORI_CABIN, 2, 'Ulasan cabin untuk ringkasan.');

        $response = $this->actingAs($viewer)
            ->getJson('/ulasan?kategori=tiket')
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('kategori', Ulasan::KATEGORI_TIKET)
            ->assertJsonPath('label_kategori', 'Tiket Renang');

        $ringkasan = $response->json('ringkasan');
        $ringkasanKategori = $response->json('ringkasan_kategori');

        $this->assertSame($sebelumTiket['jumlah_ulasan'] + 2, $ringkasan['jumlah_ulasan']);
        $this->assertSame($sebelumCabin['jumlah_ulasan'] + 1, $ringkasanKategori['cabin']['jumlah_ulasan']);
        $this->assertEqualsWithDelta(
            round((float) Ulasan::where('kategori', Ulasan::KATEGORI_TIKET)->avg('rating'), 1),
            $ringkasan['rata_rata'],
            0.001
        );

        $data = $response->json('data');
        $ids = array_column($data, 'id');

        $this->assertNotContains($cabin->id, $ids, true);

        foreach ($data as $item) {
            $this->assertSame(Ulasan::KATEGORI_TIKET, $item['kategori']);
        }
    }

    public function test_ulasan_bisa_difilter_dan_diurutkan_per_kategori(): void
    {
        $viewer = $this->pengguna('ulasan-sort-viewer');
        $userA = $this->pengguna('ulasan-sort-a');
        $userB = $this->pengguna('ulasan-sort-b');
        $userC = $this->pengguna('ulasan-sort-c');

        $ratingTiga = $this->ulasan($userA, Ulasan::KATEGORI_TIKET, 3, 'Ulasan tiket rating tiga yang cukup panjang.');
        Ulasan::whereKey($ratingTiga->id)->update(['created_at' => now()->subMinutes(3)]);

        $ratingLima = $this->ulasan($userB, Ulasan::KATEGORI_TIKET, 5, 'Ulasan tiket rating lima yang cukup panjang.');
        Ulasan::whereKey($ratingLima->id)->update(['created_at' => now()->subMinutes(2)]);

        $cabin = $this->ulasan($userC, Ulasan::KATEGORI_CABIN, 1, 'Ulasan cabin rating satu yang cukup panjang.');
        Ulasan::whereKey($cabin->id)->update(['created_at' => now()->subMinute()]);

        $tertinggi = $this->actingAs($viewer)
            ->getJson('/ulasan?kategori=tiket&sort=rating')
            ->assertOk()
            ->assertJsonPath('sort', 'rating')
            ->json('data');

        $tertinggiIds = array_column($tertinggi, 'id');

        $this->assertLessThan(
            array_search($ratingTiga->id, $tertinggiIds, true),
            array_search($ratingLima->id, $tertinggiIds, true)
        );
        $this->assertNotContains($cabin->id, $tertinggiIds, true);

        $terbaru = $this->actingAs($viewer)
            ->getJson('/ulasan?kategori=tiket&sort=terbaru')
            ->assertOk()
            ->assertJsonPath('sort', 'terbaru')
            ->json('data');

        $terbaruIds = array_column($terbaru, 'id');

        $this->assertLessThan(
            array_search($ratingTiga->id, $terbaruIds, true),
            array_search($ratingLima->id, $terbaruIds, true)
        );
        $this->assertNotContains($cabin->id, $terbaruIds, true);
    }

    public function test_badge_rating_dashboard_menampilkan_dua_kategori(): void
    {
        $user = $this->pengguna('ulasan-badge');
        $lain = $this->pengguna('ulasan-badge-lain');

        $this->ulasan($lain, Ulasan::KATEGORI_TIKET, 4, 'Ulasan tiket untuk badge dashboard.');
        $this->ulasan($lain, Ulasan::KATEGORI_CABIN, 5, 'Ulasan cabin untuk badge dashboard.');

        Tiket::create([
            'nama_tiket' => 'Tiket Uji Badge',
            'harga' => 50000,
            'stok' => 10,
            'kategori' => 'dewasa',
        ]);

        Cabin::create([
            'nama_cabin' => 'Cabin Uji Badge',
            'harga_per_malam' => 750000,
            'kapasitas' => 2,
            'status' => 'tersedia',
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Tiket Uji Badge')
            ->assertSee('Cabin Uji Badge')
            ->assertSee('data-rating-kategori="tiket"', false)
            ->assertSee('data-rating-kategori="cabin"', false)
            ->assertSee(route('ulasan.index', ['kategori' => Ulasan::KATEGORI_TIKET]), false)
            ->assertSee(route('ulasan.index', ['kategori' => Ulasan::KATEGORI_CABIN]), false);
    }

    public function test_halaman_ulasan_menampilkan_tab_kategori_dan_opsi_urutan(): void
    {
        $user = $this->pengguna('ulasan-halaman');

        $this->actingAs($user)
            ->get('/ulasan')
            ->assertOk()
            ->assertSee('Rating Tiket Renang')
            ->assertSee('Ulasan Tiket Renang')
            ->assertSee('Rating tertinggi')
            ->assertSee('Rating terbaru')
            ->assertSee('data-rating-average', false)
            ->assertSee('data-rating-count', false);

        $this->actingAs($user)
            ->get('/ulasan?kategori=cabin')
            ->assertOk()
            ->assertSee('Rating Cabin')
            ->assertSee('Ulasan Cabin');

        $this->actingAs($user)
            ->get('/ulasan?partial=1')
            ->assertOk()
            ->assertDontSee('<html', false);
    }

    public function test_menu_ulasan_selalu_muncul_untuk_pengunjung(): void
    {
        $user = $this->pengguna('ulasan-menu');

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('href="'.route('ulasan.index').'"', false);
    }

    public function test_cta_rating_di_halaman_tiket_sesuai_kategori(): void
    {
        $user = $this->pengguna('ulasan-cta');
        $tiket = $this->transaksi($user, Ulasan::KATEGORI_TIKET);
        $cabin = $this->transaksi($user, Ulasan::KATEGORI_CABIN);

        $this->actingAs($user)
            ->get('/tiket?trx='.$tiket->id)
            ->assertOk()
            ->assertSee('Beri Rating Tiket Renang')
            ->assertSee(route('ulasan.index', ['kategori' => Ulasan::KATEGORI_TIKET]), false);

        $this->actingAs($user)
            ->get('/tiket?trx='.$cabin->id)
            ->assertOk()
            ->assertSee('Beri Rating Cabin')
            ->assertSee(route('ulasan.index', ['kategori' => Ulasan::KATEGORI_CABIN]), false);
    }

    public function test_validasi_rating_dan_komentar(): void
    {
        $user = $this->pengguna('ulasan-validasi');
        $this->transaksi($user, Ulasan::KATEGORI_TIKET);

        foreach ([0, 6, 4.5, -1] as $rating) {
            $this->actingAs($user)
                ->postJson('/ulasan', [
                    'rating' => $rating,
                    'komentar' => 'Komentar yang cukup panjang.',
                ])
                ->assertStatus(422)
                ->assertJsonValidationErrors('rating');
        }

        $this->actingAs($user)
            ->postJson('/ulasan', [
                'rating' => 4,
                'komentar' => 'pendek',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('komentar')
            ->assertJsonPath('errors.komentar.0', 'Komentar minimal 10 karakter.');

        $this->assertDatabaseMissing('ulasans', ['pengguna_id' => $user->id]);
    }

    public function test_guest_cannot_access_ulasan_and_admin_cannot_submit(): void
    {
        $this->get('/ulasan')->assertRedirect('/login');

        $admin = $this->pengguna('ulasan-admin', 'admin');

        $this->actingAs($admin)
            ->postJson('/ulasan', [
                'rating' => 5,
                'komentar' => 'Admin tidak boleh mengirim ulasan.',
            ])
            ->assertForbidden();
    }
}
