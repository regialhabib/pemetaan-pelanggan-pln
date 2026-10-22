<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePelangganRequest;
use App\Http\Requests\UpdatePelangganRequest;
use App\Imports\PelangganImport;
use App\Models\Pelanggan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

class PelangganController extends Controller
{
    public function pelanggan(): View
    {
        $pelanggans = Pelanggan::all();

        return view('pelanggan.index', compact('pelanggans'));
    }

    /**
     * Data pelanggan untuk peta admin.
     */
    public function index(): JsonResponse
    {
        $pelanggan = Pelanggan::query()->get([
            'id',
            'id_pelanggan',
            'nama',
            'no_hp',
            'alamat',
            'latitude',
            'longitude',
            'golongan_tarif',
            'daya',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data pelanggan berhasil diambil',
            'data' => $pelanggan,
        ]);
    }

    public function store(StorePelangganRequest $request): RedirectResponse
    {
        Pelanggan::create($request->validated());

        return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan berhasil disimpan.');
    }

    public function update(UpdatePelangganRequest $request): RedirectResponse
    {
        Pelanggan::findOrFail($request->validated('id'))
            ->update($request->safe()->except('id'));

        return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(Pelanggan $pelanggan): RedirectResponse
    {
        $pelanggan->delete();

        return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan berhasil dihapus.');
    }

    /**
     * Antrekan import Excel dan kembalikan key untuk polling progres.
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls'],
        ]);

        $file = $request->file('file');

        try {
            $highestRow = IOFactory::load($file->getRealPath())->getActiveSheet()->getHighestDataRow();

            if ($highestRow < 2) {
                return response()->json([
                    'error' => true,
                    'message' => 'File excel kosong atau tidak memiliki data',
                ], 422);
            }

            $key = (string) Str::uuid();

            Cache::put("import_total_{$key}", $highestRow - 1, 600);
            Cache::put("import_progress_{$key}", 0, 600);

            Excel::queueImport(new PelangganImport($key), $file);

            return response()->json(['key' => $key]);
        } catch (Throwable $e) {
            Log::error('IMPORT EXCEL GAGAL', ['exception' => $e]);

            return response()->json([
                'error' => true,
                'message' => 'Import gagal diproses. Periksa format file lalu coba lagi.',
            ], 500);
        }
    }

    public function progress(string $key): JsonResponse
    {
        $total = (int) Cache::get("import_total_{$key}", 0);
        $done = (int) Cache::get("import_progress_{$key}", 0);

        if ($total <= 0) {
            return response()->json(['progress' => 0]);
        }

        return response()->json([
            'progress' => min(100, intdiv($done * 100, $total)),
        ]);
    }
}
