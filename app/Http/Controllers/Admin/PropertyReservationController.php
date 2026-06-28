<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyReservation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyReservationController extends Controller
{
    public function index(): View
    {
        return view('admin.reservations.index', [
            'reservations' => PropertyReservation::with('property.owner')->latest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.reservations.form', $this->formData(new PropertyReservation()));
    }

    public function store(Request $request): RedirectResponse
    {
        PropertyReservation::create($this->payload($request));

        return redirect()->route('admin.reservations.index')->with('status', 'Reservation created.');
    }

    public function edit(PropertyReservation $reservation): View
    {
        return view('admin.reservations.form', $this->formData($reservation));
    }

    public function update(Request $request, PropertyReservation $reservation): RedirectResponse
    {
        $reservation->update($this->payload($request));

        return redirect()->route('admin.reservations.index')->with('status', 'Reservation updated.');
    }

    public function destroy(PropertyReservation $reservation): RedirectResponse
    {
        $reservation->delete();

        return redirect()->route('admin.reservations.index')->with('status', 'Reservation deleted.');
    }

    private function formData(PropertyReservation $reservation): array
    {
        return [
            'reservation' => $reservation,
            'properties' => Property::with('owner')->orderBy('name')->get(),
        ];
    }

    private function payload(Request $request): array
    {
        return $request->validate([
            'property_id' => ['required', 'exists:properties,id'],
            'check_in_date' => ['required', 'date'],
            'check_out_date' => ['required', 'date', 'after_or_equal:check_in_date'],
            'number_of_guests' => ['required', 'integer', 'min:1'],
            'guest_name' => ['required', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'remarks' => ['nullable', 'string'],
        ]);
    }
}
