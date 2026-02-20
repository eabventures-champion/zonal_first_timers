<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Church;
use App\Services\FirstTimerService;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function __construct(
        private FirstTimerService $service
    ) {
    }

    public function index(Request $request)
    {
        $filters = $request->only(['church_id', 'search', 'date_from', 'date_to']);
        $members = $this->service->getMembers($filters);
        $churches = Church::all();
        return view('admin.members.index', compact('members', 'churches', 'filters'));
    }
}
