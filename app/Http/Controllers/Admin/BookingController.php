<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['userApp', 'service.category', 'service.professional', 'promoCode', 'review'])
            ->orderByDesc('service_day');

        // Si quieres filtro por estado:
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filtro rápido por usuario
        if ($request->has('user') && $request->user != '') {
            $query->whereHas('userApp', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user . '%');
            });
        }

        $bookings = $query->paginate(30);

        return view('admin.bookings.index', compact('bookings'));
    }

    public function edit(Booking $booking)
    {
        $booking->load(['userApp', 'service.category', 'service.professional', 'promoCode', 'review']);
        return view('admin.bookings.edit', compact('booking'));
    }

    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|string',
            'price' => 'required|numeric',
            // otros campos si quieres permitir editar
        ]);

        $booking->update($request->only(['status', 'price']));
        return redirect()->route('admin.bookings.index')->with('success', 'Reserva actualizada.');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return back()->with('success', 'Reserva eliminada.');
    }

    // Cancelación desde el panel admin
    public function cancel(Request $request, Booking $booking)
    {
        // Aquí puedes llamar a tu lógica de cancelación o simplemente marcar como cancelada.
        $booking->status = 'cancelled';
        $booking->cancelled_at = now();
        $booking->save();

        return back()->with('success', 'Reserva cancelada.');
    }
}
