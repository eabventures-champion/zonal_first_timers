<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Church;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(
        private \App\Services\ChurchHierarchyService $hierarchyService
    ) {
    }

    public function index()
    {
        $users = User::with(['roles', 'church'])->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        $categories = $this->hierarchyService->getAllCategoriesWithHierarchy();
        return view('admin.users.create', compact('roles', 'categories'));
    }

    public function store(Request $request)
    {
        $isRO = $request->role === 'Retaining Officer';

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'password' => [$isRO ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'role' => 'required|exists:roles,name',
            'church_id' => 'nullable|exists:churches,id',
            'phone' => 'required|string|max:20',
        ]);

        $password = $request->password ?: ($isRO ? $request->phone : null);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'church_id' => $request->church_id,
            'phone' => $request->phone,
        ]);

        $user->assignRole($request->role);

        // Sync church RO if user is a Retaining Officer
        if ($request->role === 'Retaining Officer' && $request->church_id) {
            Church::where('id', $request->church_id)->update(['retaining_officer_id' => $user->id]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $categories = $this->hierarchyService->getAllCategoriesWithHierarchy();

        // Find current hierarchy for pre-selection
        $currentChurch = $user->church?->load('group');
        $currentGroupId = $currentChurch?->church_group_id;
        $currentCategoryId = $currentChurch?->group?->church_category_id;

        return view('admin.users.edit', compact('user', 'roles', 'categories', 'currentGroupId', 'currentCategoryId'));
    }

    public function update(Request $request, User $user)
    {
        $isRO = $request->role === 'Retaining Officer';

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'church_id' => 'nullable|exists:churches,id',
            'phone' => 'required|string|max:20',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'church_id' => $request->church_id,
            'phone' => $request->phone,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        } elseif ($isRO && !$user->password) {
            // If the user is an RO and currently has no password set,
            // or if they are being assigned as an RO and no password was provided,
            // default the password to their phone number.
            $user->update(['password' => Hash::make($request->phone)]);
        }

        $user->syncRoles([$request->role]);

        // Sync church RO
        if ($request->role === 'Retaining Officer') {
            // First clear any existing assignment for this user to ensure consistency
            Church::where('retaining_officer_id', $user->id)->update(['retaining_officer_id' => null]);

            if ($request->church_id) {
                Church::where('id', $request->church_id)->update(['retaining_officer_id' => $user->id]);
            }
        } else {
            // If role changed from RO, clear previous assignment
            Church::where('retaining_officer_id', $user->id)->update(['retaining_officer_id' => null]);
        }

        // Sync linked FirstTimer record if one exists
        $firstTimer = \App\Models\FirstTimer::where('user_id', $user->id)->first();
        if ($firstTimer) {
            $firstTimer->update([
                'full_name' => $request->name,
                'primary_contact' => $request->phone,
                'church_id' => $request->church_id ?? $firstTimer->church_id,
            ]);
        }

        // Sync linked Member record if one exists
        $member = \App\Models\Member::where('user_id', $user->id)->first();
        if ($member) {
            $member->update([
                'full_name' => $request->name,
                'primary_contact' => $request->phone,
                'church_id' => $request->church_id ?? $member->church_id,
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
