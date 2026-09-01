<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Property;
use App\Models\PropertyReservation;
use App\Support\OwnerReservationNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class ReservationController extends Controller
{
    public function create(Request $request): View
    {
        $owner = $this->owner($request);

        return view('owner.reservations.form', $this->formData($owner, new PropertyReservation()));
    }

    public function store(Request $request): RedirectResponse
    {
        $owner = $this->owner($request);

        $reservation = PropertyReservation::create($this->payload($request, $owner));
        $whatsappUrl = null;

        try {
            $whatsappUrl = app(OwnerReservationNotification::class)->send($reservation);
        } catch (Throwable $exception) {
            Log::warning('Owner reservation notification failed after reservation was saved.', [
                'reservation_id' => $reservation->id,
                'message' => $exception->getMessage(),
            ]);
        }

        $redirect = redirect()
            ->to(route('owner.dashboard') . '#reservations')
            ->with('status', 'Reservation created.');

        if ($whatsappUrl) {
            $redirect->with('whatsapp_reservation_url', $whatsappUrl);
        }

        return $redirect;
    }

    public function edit(Request $request, PropertyReservation $reservation): View
    {
        $owner = $this->owner($request);
        $reservation = $this->ownedReservation($owner, $reservation);

        return view('owner.reservations.form', $this->formData($owner, $reservation));
    }

    public function update(Request $request, PropertyReservation $reservation): RedirectResponse
    {
        $owner = $this->owner($request);
        $reservation = $this->ownedReservation($owner, $reservation);

        $reservation->update($this->payload($request, $owner));

        return redirect()
            ->to(route('owner.dashboard') . '#reservations')
            ->with('status', 'Reservation updated.');
    }

    public function destroy(Request $request, PropertyReservation $reservation): RedirectResponse
    {
        $owner = $this->owner($request);
        $reservation = $this->ownedReservation($owner, $reservation);

        $reservation->delete();

        return redirect()
            ->to(route('owner.dashboard') . '#reservations')
            ->with('status', 'Reservation deleted.');
    }

    private function formData(Owner $owner, PropertyReservation $reservation): array
    {
        return [
            'owner' => $owner,
            'reservation' => $reservation,
            'properties' => Property::where('owner_id', $owner->id)->orderBy('name')->get(),
        ];
    }

    private function payload(Request $request, Owner $owner): array
    {
        return $request->validate([
            'property_id' => [
                'required',
                Rule::exists('properties', 'id')->where('owner_id', $owner->id),
            ],
            'check_in_date' => ['required', 'date'],
            'check_out_date' => ['required', 'date', 'after_or_equal:check_in_date'],
            'number_of_guests' => ['required', 'integer', 'min:1'],
            'guest_name' => ['required', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'remarks' => ['nullable', 'string'],
        ]);
    }

    private function owner(Request $request): Owner
    {
        return Owner::findOrFail($request->session()->get('owner_id'));
    }

    private function ownedReservation(Owner $owner, PropertyReservation $reservation): PropertyReservation
    {
        $reservation->loadMissing('property');

        abort_unless((int) $reservation->property?->owner_id === (int) $owner->id, 403);

        return $reservation;
    }
}
