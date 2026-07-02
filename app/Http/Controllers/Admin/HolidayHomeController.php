<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HolidayHome;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HolidayHomeController extends Controller
{
    public function index(): View
    {
        return view('admin.holiday-homes.index', [
            'holidayHomes' => HolidayHome::orderBy('sort_order')->latest()->paginate(15),
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
        $validated = $request->validate([
            'area_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:5120'],
            'image_url' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'number_of_bedrooms' => ['required', 'integer', 'min:0'],
            'maximum_number_of_guests' => ['required', 'integer', 'min:1'],
            'online_booking_link' => ['nullable', 'url', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image_url'] = Storage::disk('public')->url(
                $validated['image']->store('holiday-homes', 'public')
            );
        }

        unset($validated['image']);
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
