<?php

namespace App\Http\Controllers;

use App\Models\Memory;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $pending = Memory::pending()->with('tags', 'reports')->latest()->paginate(20);
        return view('admin.index', compact('pending'));
    }

    public function approve(Memory $memory)
    {
        $memory->update(['status' => 'approved']);
        return back()->with('success', 'Memory approved.');
    }

    public function reject(Memory $memory)
    {
        $memory->update(['status' => 'rejected']);
        return back()->with('success', 'Memory rejected.');
    }
}
