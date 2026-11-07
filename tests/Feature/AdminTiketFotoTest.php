<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use App\Models\Tiket;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTiketFotoTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): Pengguna
    {
        return Pengguna::create([
            'nama_pengguna' => 'Admin Foto',
            'email' => 'admin-foto-'.uniqid().'@jiwanta.test',
            'password' => 'rahasia123',
            'nohp' => '081200008888',
            'role' => 'admin',
        ]);
    }

    private function tiket(array $overrides = []): Tiket
    {
        return Tiket::create(array_merge([
            'nama_tiket' => 'Tiket Kolam Uji',
            'deskripsi' => 'Akses kolam uji.',
            'harga' => 25000,
            'stok' => 50,
            'kategori' => 'dewasa',
        ], $overrides));
    }

    public function test_admin_can_create_tiket_with_photo_and_facilities(): void
    {
        Storage::fake('public');

        $res = $this->actingAs($this->admin())->post('/admin/tiket', [
            'name' => 'Tiket Kolam Keluarga',
            'price' => 35000,
            'cap' => 80,
            'desc' => 'Akses kolam keluarga seharian.',
            'fasilitas' => 'Gazebo Publik, Kamar Bilas Standar, Loker',
            'gambar' => UploadedFile::fake()->image('kolam.jpg', 800, 600),
        ], ['Accept' => 'application/json']);

        $res->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('data.facilities', ['Gazebo Publik', 'Kamar Bilas Standar', 'Loker'])
            ->assertJsonPath('data.cap', 80);

        $tiket = Tiket::where('nama_tiket', 'Tiket Kolam Keluarga')->firstOrFail();
        $this->assertNotNull($tiket->gambar);
        Storage::disk('public')->assertExists($tiket->gambar);
        $this->assertStringContainsString('storage/'.$tiket->gambar, $res->json('data.image'));
    }

    public function test_admin_can_replace_photo_and_old_file_is_deleted(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $old = UploadedFile::fake()->image('lama.jpg')->store('tikets', 'public');
        $tiket = $this->tiket(['gambar' => $old, 'fasilitas' => 'Gazebo Publik']);

        $res = $this->actingAs($admin)->post('/admin/tiket/'.$tiket->id, [
            '_method' => 'PUT',
            'name' => $tiket->nama_tiket,
            'price' => $tiket->harga,
            'cap' => $tiket->stok,
            'desc' => $tiket->deskripsi,
            'fasilitas' => 'Loker',
            'gambar' => UploadedFile::fake()->image('baru.png', 640, 480),
        ], ['Accept' => 'application/json']);

        $res->assertOk()->assertJsonPath('data.facilities', ['Loker']);

        Storage::disk('public')->assertMissing($old);
        $tiket->refresh();
        $this->assertNotSame($old, $tiket->gambar);
        Storage::disk('public')->assertExists($tiket->gambar);
    }

    public function test_pengunjung_pages_show_uploaded_tiket_photo(): void
    {
        Storage::fake('public');
        $gambar = UploadedFile::fake()->image('kolam.jpg')->store('tikets', 'public');
        $this->tiket(['nama_tiket' => 'Tiket Foto Tamu', 'gambar' => $gambar, 'fasilitas' => 'Loker, Gazebo']);

        $user = Pengguna::create([
            'nama_pengguna' => 'Tamu Foto',
            'email' => 'tamu-foto-'.uniqid().'@gmail.com',
            'password' => 'rahasia123',
            'nohp' => '081300007777',
            'role' => 'pengunjung',
        ]);

        $this->actingAs($user)->get('/dashboard')->assertOk()
            ->assertSee(asset('storage/'.$gambar), false)
            ->assertSee('Loker');

        $this->actingAs($user)->get('/reservasi')->assertOk()
            ->assertSee(asset('storage/'.$gambar), false);
    }

    public function test_admin_tiket_page_exposes_uploaded_photo_url(): void
    {
        Storage::fake('public');
        $gambar = UploadedFile::fake()->image('kolam.jpg')->store('tikets', 'public');
        $this->tiket(['nama_tiket' => 'Tiket Foto Uji', 'gambar' => $gambar]);

        $res = $this->actingAs($this->admin())->get('/admin/tiket-renang')->assertOk();

        $tickets = collect($res->viewData('tickets'));
        $this->assertTrue($tickets->contains(fn ($t) => str_contains((string) $t['image'], $gambar)));
    }

    public function test_json_update_without_fasilitas_keeps_existing_facilities(): void
    {
        $tiket = $this->tiket(['fasilitas' => 'Gazebo Publik, Loker']);

        $this->actingAs($this->admin())->putJson('/admin/tiket/'.$tiket->id, [
            'name' => $tiket->nama_tiket,
            'price' => $tiket->harga,
            'cap' => $tiket->stok,
            'desc' => $tiket->deskripsi,
            'facilities' => ['Gazebo Publik', 'Loker'],
        ])->assertOk();

        $this->assertSame('Gazebo Publik, Loker', $tiket->refresh()->fasilitas);
    }

    public function test_admin_can_delete_tiket_and_its_photo(): void
    {
        Storage::fake('public');
        $gambar = UploadedFile::fake()->image('hapus.jpg')->store('tikets', 'public');
        $tiket = $this->tiket(['nama_tiket' => 'Tiket Hapus Uji', 'gambar' => $gambar]);

        $this->actingAs($this->admin())
            ->delete('/admin/tiket/'.$tiket->id, [], ['Accept' => 'application/json'])
            ->assertOk();

        Storage::disk('public')->assertMissing($gambar);
        $this->assertDatabaseMissing('tikets', ['id' => $tiket->id]);
    }
}
