<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HolidayHome;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HolidayHomeController extends Controller
{
    public function index(): View
    {
        return view('admin.holiday-homes.index', [
            'holidayHomes' => HolidayHome::latest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.holiday-homes.form', ['holidayHome' => new HolidayHome()]);
    }

    public function store(Request $request): RedirectResponse
    {
        HolidayHome::create($this->payload($request));

        return redirect()->route('admin.holiday-homes.index')->with('status', 'Holiday home created.');
    }

    public function edit(HolidayHome $holidayHome): View
    {
        return view('admin.holiday-homes.form', ['holidayHome' => $holidayHome]);
    }

    public function update(Request $request, HolidayHome $holidayHome): RedirectResponse
    {
        $holidayHome->update($this->payload($request));

        return redirect()->route('admin.holiday-homes.index')->with('status', 'Holiday home updated.');
    }

    public function destroy(HolidayHome $holidayHome): RedirectResponse
    {
        $holidayHome->delete();

        return redirect()->route('admin.holiday-homes.index')->with('status', 'Holiday home deleted.');
    }

    private function payload(Request $request): array
    {
        return $request->validate([
            'area_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'number_of_bedrooms' => ['required', 'integer', 'min:0'],
            'maximum_number_of_guests' => ['required', 'integer', 'min:1'],
            'online_booking_link' => ['nullable', 'url', 'max:500'],
        ]);
    }
}
