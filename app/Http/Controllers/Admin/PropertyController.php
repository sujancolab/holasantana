<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function index(): View
    {
        return view('admin.properties.index', [
            'properties' => Property::with('owner')->latest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.properties.form', $this->formData(new Property()));
    }

    public function store(Request $request): RedirectResponse
    {
        Property::create($this->payload($request));

        return redirect()->route('admin.properties.index')->with('status', 'Property created.');
    }

    public function edit(Property $property): View
    {
        return view('admin.properties.form', $this->formData($property));
    }

    public function update(Request $request, Property $property): RedirectResponse
    {
        $property->update($this->payload($request));

        return redirect()->route('admin.properties.index')->with('status', 'Property updated.');
    }

    public function destroy(Property $property): RedirectResponse
    {
        $property->delete();

        return redirect()->route('admin.properties.index')->with('status', 'Property deleted.');
    }

    private function formData(Property $property): array
    {
        return [
            'property' => $property,
            'owners' => Owner::orderBy('name')->get(),
            'types' => Property::TYPES,
        ];
    }

    private function payload(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(Property::TYPES)],
            'other_type' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'owner_id' => ['required', 'exists:owners,id'],
            'laundry_included' => ['nullable', 'boolean'],
            'check_in_included' => ['nullable', 'boolean'],
            'cleaning_included' => ['nullable', 'boolean'],
            'management_included' => ['nullable', 'boolean'],
            'full_service_included' => ['nullable', 'boolean'],
            'price_per_service' => ['nullable', 'numeric', 'min:0'],
            'annual_price' => ['nullable', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string'],
        ]);

        foreach (['laundry_included', 'check_in_included', 'cleaning_included', 'management_included', 'full_service_included'] as $field) {
            $validated[$field] = $request->boolean($field);
        }

        return $validated;
    }
}
