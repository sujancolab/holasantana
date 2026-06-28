<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OwnerController extends Controller
{
    public function index(): View
    {
        return view('admin.owners.index', [
            'owners' => Owner::latest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.owners.form', ['owner' => new Owner()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Owner::create($this->payload($request));

        return redirect()->route('admin.owners.index')->with('status', 'Owner created.');
    }

    public function edit(Owner $owner): View
    {
        return view('admin.owners.form', ['owner' => $owner]);
    }

    public function update(Request $request, Owner $owner): RedirectResponse
    {
        $owner->update($this->payload($request, $owner));

        return redirect()->route('admin.owners.index')->with('status', 'Owner updated.');
    }

    public function destroy(Owner $owner): RedirectResponse
    {
        $owner->delete();

        return redirect()->route('admin.owners.index')->with('status', 'Owner deleted.');
    }

    private function payload(Request $request, ?Owner $owner = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'google_photo_album_link' => ['nullable', 'url', 'max:500'],
            'owner_user_id' => ['required', 'string', 'max:100', Rule::unique('owners', 'owner_user_id')->ignore($owner)],
            'owner_password' => [$owner ? 'nullable' : 'required', 'string', 'min:8', 'max:255'],
        ]);

        if (filled($validated['owner_password'] ?? null)) {
            $validated['owner_password'] = Hash::make($validated['owner_password']);
        } else {
            unset($validated['owner_password']);
        }

        return $validated;
    }
}
