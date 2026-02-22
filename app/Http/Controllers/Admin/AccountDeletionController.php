<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class AccountDeletionController extends Controller
{
    public function index()
    {
        $requests = User::whereNotNull('deletion_requested_at')
            ->orderBy('deletion_requested_at')
            ->get();

        return view('admin.account-deletions.index', compact('requests'));
    }

    public function approve(User $user)
    {
        $user->delete();
        return Redirect::route('admin.account-deletions.index')
            ->with('success', "Account for {$user->name} has been permanently deleted.");
    }

    public function deny(User $user)
    {
        $user->update([
            'deletion_requested_at' => null,
        ]);
        return Redirect::route('admin.account-deletions.index')
            ->with('success', "Deletion request for {$user->name} has been denied.");
    }
}
