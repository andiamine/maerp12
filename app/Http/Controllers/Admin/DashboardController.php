<?php
// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cabinet;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getStats()
    {
        return response()->json([
            'cabinets' => [
                'total' => Cabinet::count(),
                'active' => Cabinet::where('statut', 'actif')->count(),
                'expired' => Cabinet::where('date_expiration', '<', now())->count(),
            ],
            'companies' => [
                'total' => Company::count(),
                'active' => Company::where('statut', 'active')->count(),
                'this_month' => Company::whereMonth('created_at', now()->month)->count(),
            ],
            'users' => [
                'total' => User::count(),
                'active' => User::where('statut', 'actif')->count(),
                'this_month' => User::whereMonth('created_at', now()->month)->count(),
            ],
        ]);
    }

    public function getCabinetsByCity()
    {
        $data = Cabinet::select('ville', DB::raw('count(*) as count'))
            ->groupBy('ville')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'labels' => $data->pluck('ville'),
            'data' => $data->pluck('count'),
        ]);
    }
}
