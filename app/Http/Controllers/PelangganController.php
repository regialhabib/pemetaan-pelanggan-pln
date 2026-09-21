<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Imports\PelangganImport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PelangganController extends Controller
{

    public function pelanggan()
    {
        $pelanggans = Pelanggan::get();
        return view('pelanggan.index', compact('pelanggans'));
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $pelanggan = Pelanggan::select([
                'id',
                'id_pelanggan',
                'nama',
                'alamat',
                'latitude',
                'longitude',
                'golongan_tarif',
                'daya'
            ])->get();

            return response()->json([
                'success' => true,
                'message' => 'Data pelanggan berhasil diambil',
                'data' => $pelanggan,

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {

        $validated = $request->validate([
            'id' => 'required',
            'nama' => 'required',
            'alamat' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'golongan_tarif' => 'required',
            'daya' => 'required',
        ]);

        try {

            $pelanggan = Pelanggan::findOrFail($request->id);
            $pelanggan->update($validated);

            return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('pelanggan.index')->with('error', 'Data pelanggan gagal diperbarui. Silakan coba lagi.');
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_pelanggan' => 'required',
            'nama' => 'required',
            'alamat' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'golongan_tarif' => 'required',
            'daya' => 'required',
        ]);

        try {
            Pelanggan::create($validated);
            return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('pelanggan.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $pelanggan = Pelanggan::findOrFail($id);
            $pelanggan->delete();
            return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('pelanggan.index')->with('error', 'Data pelanggan gagal dihapus. Silakan coba lagi.');
        }
    }

    public function import(Request $request)
    {
        try {

            // ===============================
            // VALIDASI
            // ===============================
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls'
            ]);

            if (!$request->hasFile('file')) {
                throw new \Exception('File tidak ditemukan di request');
            }

            $file = $request->file('file');

            // ===============================
            // HITUNG TOTAL ROW
            // ===============================
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();

            $highestRow = $sheet->getHighestDataRow();

            if ($highestRow < 2) {
                throw new \Exception('File excel kosong atau tidak memiliki data');
            }

            $totalRows = $highestRow - 1;

            // ===============================
            // SET CACHE
            // ===============================
            $key = (string) Str::uuid();

            Cache::put("import_total_{$key}", $totalRows, 600);
            Cache::put("import_progress_{$key}", 0, 600);

            // ===============================
            // QUEUE IMPORT
            // ===============================
            Excel::queueImport(new PelangganImport($key), $file);

           // Excel::import(new PelangganImport($key), $file);


            return response()->json([
                'key' => $key
            ]);
        } catch (\Throwable $e) {

            // ===============================
            // LOG ERROR DETAIL
            // ===============================
            Log::error('IMPORT EXCEL GAGAL', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error'   => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function progress($key)
    {
        try {
            $total = Cache::get("import_total_{$key}");
            $done  = Cache::get("import_progress_{$key}");

            if (!$total || $total <= 0) {
                return response()->json([
                    'progress' => 0
                ]);
            }

            $progress = intval(($done / $total) * 100);

            return response()->json([
                'progress' => min(100, $progress)
            ]);
        } catch (\Throwable $e) {

            Log::error('IMPORT PROGRESS ERROR', [
                'key'     => $key,
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'progress' => 0,
                'error' => true
            ], 500);
        }
    }
}
