<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPelanggan = \App\Models\Pelanggan::count();
        $totalDaya = \App\Models\Pelanggan::sum('daya');
        
        $tarifGroups = \App\Models\Pelanggan::select('golongan_tarif', \DB::raw('count(*) as total'))
            ->groupBy('golongan_tarif')
            ->pluck('total', 'golongan_tarif');
            
        $dayaGroups = \App\Models\Pelanggan::select('daya', \DB::raw('count(*) as total'))
            ->groupBy('daya')
            ->pluck('total', 'daya');

        // 🔢 Summary cards
        $summary = [
            'total_pelanggan' => $totalPelanggan,
            'total_daya'      => $totalDaya, // VA
            'jumlah_tarif'    => $tarifGroups->count(),
            'jumlah_wilayah'  => $dayaGroups->count(),
        ];

        // 📊 Chart: Distribusi tarif
        $tarifChart = [
            'labels' => $tarifGroups->keys()->toArray(),
            'data'   => $tarifGroups->values()->toArray(),
        ];

        // 📊 Chart: Pelanggan per wilayah (changed to Daya for real data)
        $wilayahChart = [
            'labels' => $dayaGroups->keys()->map(fn($val) => $val . ' VA')->toArray(),
            'data'   => $dayaGroups->values()->toArray(),
        ];

        return view('dashboard.index', compact(
            'summary',
            'tarifChart',
            'wilayahChart'
        ));
    }

    public function profile()
    {
        return view('profile');
    }
    public function profileUpdate(Request $request)
    {
        
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::user()->id,
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:3|max:12|required_with:current_password',
            'password_confirmation' => 'nullable|min:3|max:12|required_with:new_password|same:new_password'
        ]);


        $user = User::findOrFail(Auth::user()->id);
        $user->nama = $request->input('nama');
       
        $user->email = $request->input('email');

        if (!is_null($request->input('current_password'))) {
            if (Hash::check($request->input('current_password'), $user->password)) {
                $user->password = $request->input('new_password');
            } else {
                return redirect()->back()->withInput();
            }
        }

        $user->save();

        return redirect()->route('profile')->withSuccess('Profile updated successfully.');
    }
}
