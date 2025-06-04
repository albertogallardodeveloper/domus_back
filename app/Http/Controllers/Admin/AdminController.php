<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\UserApp;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Conversation;

class AdminController extends Controller
{
    public function index()
    {
        // Contadores rápidos
        $usersCount = UserApp::count();
        $bookingsCount = Booking::count();
        $servicesCount = Service::count();
        $conversationsCount = Conversation::count();

        // Últimas reservas y usuarios (top 5)
        $latestBookings = Booking::with(['userApp', 'service'])
            ->latest('created_at')
            ->take(5)
            ->get();

        $latestUsers = UserApp::latest('created_at')
            ->take(5)
            ->get();

        // Reservas por mes (últimos 12 meses, incluye meses vacíos)
        $start = Carbon::now()->subMonths(11)->startOfMonth();
        $months = [];
        $counts = [];

        for ($i = 0; $i < 12; $i++) {
            $date = $start->copy()->addMonths($i);
            $months[] = $date->format('M Y');
            $counts[] = 0;
        }

        // Agrupa reservas por mes
        $reservas = DB::table('bookings')
            ->select(DB::raw('DATE_FORMAT(service_day, "%b %Y") as mes'), DB::raw('count(*) as total'))
            ->where('service_day', '>=', $start)
            ->groupBy('mes')
            ->orderByRaw('MIN(service_day)')
            ->pluck('total', 'mes')
            ->toArray();

        foreach ($months as $idx => $label) {
            if (isset($reservas[$label])) {
                $counts[$idx] = $reservas[$label];
            }
        }

        return view('admin.dashboard', [
            'usersCount'      => $usersCount,
            'bookingsCount'   => $bookingsCount,
            'servicesCount'   => $servicesCount,
            'conversationsCount' => $conversationsCount,
            'latestBookings'  => $latestBookings,
            'latestUsers'     => $latestUsers,
            'months'          => $months,
            'reservasPorMes'  => $counts,
        ]);
    }
}
