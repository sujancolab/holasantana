<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Owner;
use App\Models\Property;
use App\Models\PropertyReservation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $owner = Owner::findOrFail($request->session()->get('owner_id'));
        $propertyIds = Property::where('owner_id', $owner->id)->pluck('id');

        return view('owner.dashboard', [
            'owner' => $owner,
            'properties' => Property::whereKey($propertyIds)->latest()->get(),
            'reservations' => PropertyReservation::with('property')
                ->whereIn('property_id', $propertyIds)
                ->latest('check_in_date')
                ->get(),
            'activities' => Activity::with('property')
                ->whereIn('property_id', $propertyIds)
                ->latest('visiting_at')
                ->get(),
        ]);
    }
}
