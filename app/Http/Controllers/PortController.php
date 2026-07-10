<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PortController extends Controller
{
    public function index(Request $request)
    {
        $region = $request->get('region', 'all');
        $search = $request->get('search', '');

        $query = DB::table('ports');

        if ($region !== 'all') {
            $query->where('region', $region);
        }
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%");
            });
        }

        $ports = $query->orderBy('name')->get();

        $regions = ['all', 'Asia', 'Europe', 'Americas', 'Middle East', 'Africa'];
        $stats = [
            'total'  => $ports->count(),
            'active' => $ports->where('status', 'active')->count(),
            'busy'   => $ports->where('status', 'busy')->count(),
        ];

        $lastSynced = DB::table('system_settings')->where('key', 'ports_last_synced_at')->value('value');
        $syncStatus = DB::table('system_settings')->where('key', 'ports_sync_status')->value('value');

        return view('port.index', compact('ports', 'regions', 'region', 'stats', 'search', 'lastSynced', 'syncStatus'));
    }

    public function api(Request $request)
    {
        $region = $request->get('region', 'all');

        $query = DB::table('ports');
        if ($region !== 'all') {
            $query->where('region', $region);
        }
        $ports = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'total'   => $ports->count(),
            'data'    => $ports,
        ]);
    }
}