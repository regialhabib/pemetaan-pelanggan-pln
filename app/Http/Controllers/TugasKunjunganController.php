<?php

namespace App\Http\Controllers;


use App\Models\DetailTugasKunjungan;
use App\Models\Pelanggan;
use App\Models\TugasKunjungan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;


class TugasKunjunganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pelanggans = Pelanggan::get();
        $petugass = User::where('role', 'petugas')->get();

        return view('tugas_kunjungan.index', compact('pelanggans', 'petugass'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function laporan()
    {
        return view('tugas_kunjungan.laporan');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'pelanggan' => 'required',
            'petugas' => 'required|exists:users,id',
            'keterangan' => 'required',

        ]);

        $pelanggan = json_decode($request->pelanggan, true);
        DB::beginTransaction();
        try {
            $tugasKunjungan = new TugasKunjungan;
            $tugasKunjungan->id_petugas = $request->petugas;
            $tugasKunjungan->keterangan = $request->keterangan;
            $tugasKunjungan->save();

            foreach ($pelanggan as $id) {
                DetailTugasKunjungan::create([
                    'id_tugas_kunjungan' => $tugasKunjungan->id,
                    'id_pelanggan' => $id
                ]);
            }
            DB::commit();
            return redirect()->route('tugas_kunjungan.index')->with('success', ' Tugas kunjungan berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('tugas_kunjungan.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {

        $tugasKunjungans = TugasKunjungan::with('detailTugas')->get();

        if (Auth::user()->role == 'petugas') {
            $tugasKunjungans = TugasKunjungan::with('detailTugas')->where('id_petugas', Auth::user()->id)->get();
        }
        return view('tugas_kunjungan.list', compact('tugasKunjungans'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function detail($id)
    {
        $tugasKunjungans = TugasKunjungan::with('detailTugas.pelanggan')->findOrFail($id);

        return view('tugas_kunjungan.detail', compact('tugasKunjungans'));
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $tugasKunjungan = TugasKunjungan::findOrFail($id);
            $tugasKunjungan->delete();
            return redirect()->route('tugas_kunjungan.show')->with('success', 'Data  berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('tugas_kunjungan.show')->with('error', 'Data  gagal dihapus. Silakan coba lagi.');
        }
    }

    public function pelanggans($id = null)
    {
        try {



            if (Auth::user()->role == 'petugas') {
                $tugasKunjungan = TugasKunjungan::with('detailTugas.pelanggan')
                    ->where('id_petugas', Auth::id())
                    ->latest()
                    ->first();
            } else {
                $tugasKunjungan = TugasKunjungan::with('detailTugas.pelanggan')
                    ->where('id', $id)->first();
            }



            if (!$tugasKunjungan) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tidak ada tugas kunjungan',
                    'data' => []
                ], 200);
            }

            // Gunakan map untuk menggabungkan data
            $dataResponse = $tugasKunjungan->detailTugas->map(function ($detail) {
                // Kita ambil data pelanggan, lalu kita tambahkan status dari detail
                $pelanggan = $detail->pelanggan;

                // Tambahkan property baru ke dalam objek pelanggan
                $pelanggan->status_kunjungan = $detail->status_kunjungan; // Sesuaikan 'status' dengan nama kolom di tabel detail_tugas
                $pelanggan->id_detail_tugas = $detail->id; // Penting untuk update status nanti

                return $pelanggan;
            });

            return response()->json([
                'success' => true,
                'message' => 'Data pelanggan berhasil diambil',
                'data' => $dataResponse,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function map()
    {
        return view('tugas_kunjungan.map');
    }

    public function updateStatus($id)
    {
        try {

            // Cari data detail
            $detail = DetailTugasKunjungan::findOrFail($id);

            // Tentukan status berikutnya
            if ($detail->status_kunjungan === 'belum_dikunjungi') {
                $detail->status_kunjungan = 'diproses';
            } elseif ($detail->status_kunjungan === 'diproses') {
                $detail->status_kunjungan = 'sudah_dikunjungi';
            } elseif ($detail->status_kunjungan === 'sudah_dikunjungi') {
                return response()->json([
                    'success' => false,
                    'message' => 'Kunjungan sudah selesai dan tidak dapat diperbarui lagi.'
                ], 400);
            }

            // Simpan perubahan status detail
            $detail->save();

            // Ambil semua detail berdasarkan tugas
            $totalDetail = DetailTugasKunjungan::where('id_tugas_kunjungan', $detail->id_tugas_kunjungan)->count();

            $totalSelesai = DetailTugasKunjungan::where('id_tugas_kunjungan', $detail->id_tugas_kunjungan)
                ->where('status_kunjungan', 'sudah_dikunjungi')
                ->count();

            // Jika semua detail selesai maka update status tugas
            if ($totalDetail == $totalSelesai) {
                TugasKunjungan::where('id', $detail->id_tugas_kunjungan)
                    ->update(['status_tugas' => 'selesai']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Status kunjungan berhasil diperbarui.',
                'data' => [
                    'id_detail' => $detail->id,
                    'status_kunjungan' => $detail->status_kunjungan
                ]
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Data detail kunjungan tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getLaporanDataFromRequest(Request $request)
    {
        $request->validate([
            'tanggal_awal'  => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
        ]);

        return DetailTugasKunjungan::with('tugas.petugas', 'pelanggan')
            ->whereBetween('created_at', [$request->tanggal_awal, $request->tanggal_akhir])
            ->get();
    }

    public function laporanData(Request $request)
    {
        $datas = $this->getLaporanDataFromRequest($request);

        return response()->json([
            'success' => true,
            'message' => 'Data laporan berhasil diambil',
            'data' => $datas,
        ], 200);
    }

    public function laporanPrint(Request $request)
    {
        $datas = $this->getLaporanDataFromRequest($request);

        $pdf = Pdf::loadView('tugas_kunjungan.pdf', [
            'data' => $datas,
            'tanggal_awal' => $request->tanggal_awal,
            'tanggal_akhir' => $request->tanggal_akhir,
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('tugas_kunjungan.pdf');
    }
}
