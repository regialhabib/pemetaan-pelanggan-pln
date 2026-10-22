<?php

namespace Tests\Feature;

use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PelangganTest extends TestCase
{
    use RefreshDatabase;

    private function pelangganData(array $overrides = []): array
    {
        return [
            'id_pelanggan' => 'PLN99999',
            'nama' => 'Siti',
            'no_hp' => '081234567890',
            'alamat' => 'Jl. Sudirman, Kota Jambi',
            'latitude' => -1.6161,
            'longitude' => 103.583,
            'golongan_tarif' => 'R1',
            'daya' => 1300,
            ...$overrides,
        ];
    }

    public function test_petugas_cannot_access_admin_pages(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('pelanggan.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_pelanggan_page_and_api(): void
    {
        $admin = User::factory()->admin()->create();
        Pelanggan::factory()->count(3)->create();

        $this->actingAs($admin)->get(route('pelanggan.index'))->assertOk();

        $this->actingAs($admin)->getJson(route('pelanggan.api'))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(3, 'data');
    }

    public function test_admin_can_store_pelanggan(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->post(route('pelanggan.store'), $this->pelangganData())
            ->assertRedirect(route('pelanggan.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('pelanggans', ['id_pelanggan' => 'PLN99999', 'nama' => 'Siti']);
    }

    public function test_store_rejects_duplicate_id_and_invalid_tarif(): void
    {
        Pelanggan::factory()->create(['id_pelanggan' => 'PLN99999']);

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('pelanggan.store'), $this->pelangganData(['golongan_tarif' => 'X9']))
            ->assertSessionHasErrors(['id_pelanggan', 'golongan_tarif']);
    }

    public function test_admin_can_update_pelanggan(): void
    {
        $pelanggan = Pelanggan::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('pelanggan.update'), [
                'id' => $pelanggan->id,
                ...$this->pelangganData(['nama' => 'Nama Diubah']),
            ])
            ->assertRedirect(route('pelanggan.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame('Nama Diubah', $pelanggan->fresh()->nama);
    }

    public function test_admin_can_delete_pelanggan(): void
    {
        $pelanggan = Pelanggan::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->delete(route('pelanggan.destroy', $pelanggan))
            ->assertRedirect(route('pelanggan.index'));

        $this->assertModelMissing($pelanggan);
    }

    public function test_import_requires_excel_file(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->postJson(route('pelanggan.import'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('file');
    }

    public function test_import_progress_without_data_is_zero(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->getJson(route('pelanggan.import.progress', 'tidak-ada'))
            ->assertOk()
            ->assertJson(['progress' => 0]);
    }

    public function test_admin_can_view_dashboard(): void
    {
        Pelanggan::factory()->count(2)->create();

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('summary', fn (array $summary) => $summary['total_pelanggan'] === 2);
    }
}
