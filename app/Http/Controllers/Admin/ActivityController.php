<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(): View
    {
        return view('admin.activities.index', [
            'activities' => Activity::with('property.owner')->latest('visiting_at')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.activities.form', $this->formData(new Activity()));
    }

    public function store(Request $request): RedirectResponse
    {
        Activity::create($this->payload($request));

        return redirect()->route('admin.activities.index')->with('status', 'Activity created.');
    }

    public function edit(Activity $activity): View
    {
        return view('admin.activities.form', $this->formData($activity));
    }

    public function update(Request $request, Activity $activity): RedirectResponse
    {
        $activity->update($this->payload($request));

        return redirect()->route('admin.activities.index')->with('status', 'Activity updated.');
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        $activity->delete();

        return redirect()->route('admin.activities.index')->with('status', 'Activity deleted.');
    }

    private function formData(Activity $activity): array
    {
        return [
            'activity' => $activity,
            'properties' => Property::with('owner')->orderBy('name')->get(),
        ];
    }

    private function payload(Request $request): array
    {
        return $request->validate([
            'property_id' => ['required', 'exists:properties,id'],
            'visiting_at' => ['required', 'date'],
            'visitor_name' => ['required', 'string', 'max:255'],
            'observation' => ['nullable', 'string'],
            'activity_performed' => ['nullable', 'string'],
            'exit_time' => ['nullable', 'date_format:H:i'],
            'remarks' => ['nullable', 'string'],
        ]);
    }
}
