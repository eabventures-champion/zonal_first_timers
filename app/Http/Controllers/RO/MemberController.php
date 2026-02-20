<?php

namespace App\Http\Controllers\RO;

use App\Http\Controllers\Controller;
use App\Services\FirstTimerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    public function __construct(
        private FirstTimerService $service
    ) {
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'date_from', 'date_to']);
        $filters['church_id'] = Auth::user()->church_id;
        $members = $this->service->getMembers($filters);
        return view('ro.members.index', compact('members', 'filters'));
    }
}
