<?php

namespace Tests\Feature;

use App\Enums\StatusKunjungan;
use App\Enums\StatusTugas;
use App\Models\DetailTugasKunjungan;
use App\Models\Pelanggan;
use App\Models\TugasKunjungan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TugasKunjunganTest extends TestCase
{
    use RefreshDatabase;

    private function tugasWithPelanggan(User $petugas, int $jumlah = 2): TugasKunjungan
    {
        $tugas = TugasKunjungan::factory()->for($petugas, 'petugas')->create();

        Pelanggan::factory()->count($jumlah)->create()->each(
            fn (Pelanggan $pelanggan) => $tugas->detailTugas()->create(['id_pelanggan' => $pelanggan->id])
        );

        return $tugas;
    }

    public function test_petugas_home_redirects_to_map(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('home'))
            ->assertRedirect(route('tugas_kunjungan.map'));
    }

    public function test_admin_can_create_tugas_with_pelanggan(): void
    {
        $petugas = User::factory()->create();
        $pelanggan = Pelanggan::factory()->count(2)->create();

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('tugas_kunjungan.store'), [
                'pelanggan' => json_encode($pelanggan->pluck('id')),
                'petugas' => $petugas->id,
                'keterangan' => 'Cek meteran',
            ])
            ->assertRedirect(route('tugas_kunjungan.index'))
            ->assertSessionHasNoErrors();

        $tugas = TugasKunjungan::sole();

        $this->assertTrue($tugas->petugas->is($petugas));
        $this->assertSame(2, $tugas->detailTugas()->count());
    }

    public function test_tugas_cannot_be_assigned_to_admin_or_without_pelanggan(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('tugas_kunjungan.store'), [
                'pelanggan' => json_encode([]),
                'petugas' => $admin->id,
                'keterangan' => 'Cek meteran',
            ])
            ->assertSessionHasErrors(['pelanggan', 'petugas']);

        $this->assertDatabaseCount('tugas_kunjungan', 0);
    }

    public function test_petugas_only_sees_own_tugas(): void
    {
        $petugas = User::factory()->create();
        $milikSendiri = $this->tugasWithPelanggan($petugas);
        $milikLain = $this->tugasWithPelanggan(User::factory()->create());

        $this->actingAs($petugas)
            ->get(route('tugas_kunjungan.show'))
            ->assertOk()
            ->assertViewHas('tugasKunjungans', fn ($tugas) => $tugas->modelKeys() === [$milikSendiri->id]);

        $this->actingAs($petugas)->get(route('tugas_kunjungan.detail', $milikSendiri))->assertOk();
        $this->actingAs($petugas)->get(route('tugas_kunjungan.detail', $milikLain))->assertForbidden();
    }

    public function test_petugas_gets_pelanggan_of_latest_tugas(): void
    {
        $petugas = User::factory()->create();
        $this->tugasWithPelanggan($petugas, 3);

        $this->actingAs($petugas)
            ->getJson(route('tugas_kunjungan.pelanggans'))
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.0.status_kunjungan', StatusKunjungan::BelumDikunjungi->value);
    }

    public function test_status_advances_and_tugas_completes(): void
    {
        $petugas = User::factory()->create();
        $tugas = $this->tugasWithPelanggan($petugas, 1);
        $detail = $tugas->detailTugas()->sole();

        $this->actingAs($petugas)
            ->postJson(route('kunjungan.update_status', $detail))
            ->assertOk()
            ->assertJsonPath('data.status_kunjungan', StatusKunjungan::Diproses->value);

        $this->actingAs($petugas)
            ->postJson(route('kunjungan.update_status', $detail))
            ->assertOk()
            ->assertJsonPath('data.status_kunjungan', StatusKunjungan::SudahDikunjungi->value);

        $this->assertSame(StatusTugas::Selesai, $tugas->fresh()->status_tugas);

        $this->actingAs($petugas)
            ->postJson(route('kunjungan.update_status', $detail))
            ->assertStatus(400);
    }

    public function test_petugas_cannot_update_status_of_other_petugas(): void
    {
        $detail = $this->tugasWithPelanggan(User::factory()->create(), 1)->detailTugas()->sole();

        $this->actingAs(User::factory()->create())
            ->postJson(route('kunjungan.update_status', $detail))
            ->assertForbidden();

        $this->assertSame(StatusKunjungan::BelumDikunjungi, $detail->fresh()->status_kunjungan);
    }

    public function test_admin_can_delete_tugas(): void
    {
        $tugas = $this->tugasWithPelanggan(User::factory()->create());

        $this->actingAs(User::factory()->admin()->create())
            ->delete(route('tugas_kunjungan.destroy', $tugas))
            ->assertRedirect(route('tugas_kunjungan.show'));

        $this->assertModelMissing($tugas);
        $this->assertDatabaseCount('detail_tugas_kunjungan', 0);
    }

    public function test_laporan_includes_records_on_end_date(): void
    {
        $tugas = $this->tugasWithPelanggan(User::factory()->create(), 1);
        DetailTugasKunjungan::query()->update(['created_at' => '2026-10-20 15:30:00']);

        $this->actingAs(User::factory()->admin()->create())
            ->getJson(route('laporan.data', ['tanggal_awal' => '2026-10-01', 'tanggal_akhir' => '2026-10-20']))
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_laporan_print_returns_pdf(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('laporan.print', ['tanggal_awal' => '2026-10-01', 'tanggal_akhir' => '2026-10-20']))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }
}
