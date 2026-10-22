<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\Pelanggan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $tarifGroups = Pelanggan::query()
            ->select('golongan_tarif', DB::raw('count(*) as total'))
            ->groupBy('golongan_tarif')
            ->pluck('total', 'golongan_tarif');

        $dayaGroups = Pelanggan::query()
            ->select('daya', DB::raw('count(*) as total'))
            ->groupBy('daya')
            ->pluck('total', 'daya');

        $summary = [
            'total_pelanggan' => Pelanggan::count(),
            'total_daya' => Pelanggan::sum('daya'), // VA
            'jumlah_tarif' => $tarifGroups->count(),
            'jumlah_wilayah' => $dayaGroups->count(),
        ];

        $tarifChart = [
            'labels' => $tarifGroups->keys()->toArray(),
            'data' => $tarifGroups->values()->toArray(),
        ];

        $wilayahChart = [
            'labels' => $dayaGroups->keys()->map(fn ($daya) => $daya.' VA')->toArray(),
            'data' => $dayaGroups->values()->toArray(),
        ];

        return view('dashboard.index', compact('summary', 'tarifChart', 'wilayahChart'));
    }

    public function profile(): View
    {
        return view('profile');
    }

    public function profileUpdate(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->safe()->only(['name', 'email']));

        if ($request->filled('new_password')) {
            $user->password = $request->validated('new_password');
        }

        $user->save();

        return redirect()->route('profile')->with('success', 'Profile updated successfully.');
    }
}
