<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Enums\StatusKunjungan;
use App\Enums\StatusTugas;
use App\Http\Requests\LaporanRequest;
use App\Http\Requests\StoreTugasKunjunganRequest;
use App\Models\DetailTugasKunjungan;
use App\Models\Pelanggan;
use App\Models\TugasKunjungan;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TugasKunjunganController extends Controller
{
    public function index(): View
    {
        $pelanggans = Pelanggan::all();
        $petugass = User::where('role', Role::Petugas)->get();

        return view('tugas_kunjungan.index', compact('pelanggans', 'petugass'));
    }

    public function store(StoreTugasKunjunganRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $tugasKunjungan = TugasKunjungan::create([
                'id_petugas' => $request->validated('petugas'),
                'keterangan' => $request->validated('keterangan'),
            ]);

            $tugasKunjungan->detailTugas()->createMany(
                collect($request->validated('pelanggan'))
                    ->map(fn (int $idPelanggan) => ['id_pelanggan' => $idPelanggan])
                    ->all()
            );
        });

        return redirect()->route('tugas_kunjungan.index')->with('success', 'Tugas kunjungan berhasil ditambahkan.');
    }

    /**
     * Daftar tugas: admin melihat semua, petugas hanya miliknya.
     */
    public function show(Request $request): View
    {
        $user = $request->user();

        $tugasKunjungans = TugasKunjungan::with('detailTugas', 'petugas')
            ->when($user->isPetugas(), fn ($query) => $query->whereBelongsTo($user, 'petugas'))
            ->get();

        return view('tugas_kunjungan.list', compact('tugasKunjungans'));
    }

    public function detail(TugasKunjungan $tugasKunjungan): View
    {
        Gate::authorize('view', $tugasKunjungan);

        $tugasKunjungans = $tugasKunjungan->load('detailTugas.pelanggan');

        return view('tugas_kunjungan.detail', compact('tugasKunjungans'));
    }

    public function destroy(TugasKunjungan $tugasKunjungan): RedirectResponse
    {
        Gate::authorize('delete', $tugasKunjungan);

        $tugasKunjungan->delete();

        return redirect()->route('tugas_kunjungan.show')->with('success', 'Data berhasil dihapus.');
    }

    /**
     * Pelanggan pada sebuah tugas beserta status kunjungannya, untuk peta.
     * Petugas selalu mendapat tugas terbarunya; admin memilih lewat ID.
     */
    public function pelanggans(Request $request, ?string $id = null): JsonResponse
    {
        $user = $request->user();

        $tugasKunjungan = TugasKunjungan::with('detailTugas.pelanggan')
            ->when(
                $user->isPetugas(),
                fn ($query) => $query->whereBelongsTo($user, 'petugas')->latest(),
                fn ($query) => $query->whereKey($id),
            )
            ->first();

        if (! $tugasKunjungan) {
            return response()->json([
                'success' => true,
                'message' => 'Tidak ada tugas kunjungan',
                'data' => [],
            ]);
        }

        $data = $tugasKunjungan->detailTugas->map(function (DetailTugasKunjungan $detail) {
            $pelanggan = $detail->pelanggan;
            $pelanggan->status_kunjungan = $detail->status_kunjungan;
            $pelanggan->id_detail_tugas = $detail->id;

            return $pelanggan;
        });

        return response()->json([
            'success' => true,
            'message' => 'Data pelanggan berhasil diambil',
            'data' => $data,
        ]);
    }

    public function map(): View
    {
        return view('tugas_kunjungan.map');
    }

    /**
     * Majukan status kunjungan satu langkah; tandai tugas selesai bila
     * semua kunjungannya sudah selesai.
     */
    public function updateStatus(DetailTugasKunjungan $detail): JsonResponse
    {
        Gate::authorize('update', $detail->tugas);

        $next = $detail->status_kunjungan->next();

        if (! $next) {
            return response()->json([
                'success' => false,
                'message' => 'Kunjungan sudah selesai dan tidak dapat diperbarui lagi.',
            ], 400);
        }

        DB::transaction(function () use ($detail, $next) {
            $detail->update(['status_kunjungan' => $next]);

            $masihBerjalan = $detail->tugas->detailTugas()
                ->where('status_kunjungan', '!=', StatusKunjungan::SudahDikunjungi)
                ->exists();

            if (! $masihBerjalan) {
                $detail->tugas->update(['status_tugas' => StatusTugas::Selesai]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Status kunjungan berhasil diperbarui.',
            'data' => [
                'id_detail' => $detail->id,
                'status_kunjungan' => $detail->status_kunjungan,
            ],
        ]);
    }

    public function laporan(): View
    {
        return view('tugas_kunjungan.laporan');
    }

    public function laporanData(LaporanRequest $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Data laporan berhasil diambil',
            'data' => $this->laporanQuery($request),
        ]);
    }

    public function laporanPrint(LaporanRequest $request): Response
    {
        return Pdf::loadView('tugas_kunjungan.pdf', [
            'data' => $this->laporanQuery($request),
            'tanggal_awal' => $request->validated('tanggal_awal'),
            'tanggal_akhir' => $request->validated('tanggal_akhir'),
        ])->setPaper('A4', 'portrait')->stream('tugas_kunjungan.pdf');
    }

    /**
     * @return Collection<int, DetailTugasKunjungan>
     */
    private function laporanQuery(LaporanRequest $request): Collection
    {
        return DetailTugasKunjungan::with('tugas.petugas', 'pelanggan')
            ->whereDate('created_at', '>=', $request->validated('tanggal_awal'))
            ->whereDate('created_at', '<=', $request->validated('tanggal_akhir'))
            ->get();
    }
}
