<?php

namespace App\Http\Controllers;

use App\Models\Annotation;
use App\Models\Memory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function enter()
    {
        // auth.basic already logged the session in by the time we get here.
        return redirect('/')->with('success', 'Admin mode on.');
    }

    public function exit()
    {
        Auth::logout();
        return redirect('/')->with('success', 'Admin mode off.');
    }

    public function index()
    {
        $pending = Memory::pending()->with('tags', 'reports')->latest()->paginate(20, ['*'], 'page');
        $pendingAnnotations = Annotation::pending()->with('memory')->latest()->paginate(20, ['*'], 'annotations_page');

        return view('admin.index', compact('pending', 'pendingAnnotations'));
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

    public function destroy(Memory $memory)
    {
        $memory->delete();
        return back()->with('success', 'Memory deleted.');
    }

    public function approveAnnotation(Annotation $annotation)
    {
        $annotation->update(['status' => 'approved']);
        return back()->with('success', 'Annotation approved.');
    }

    public function rejectAnnotation(Annotation $annotation)
    {
        $annotation->update(['status' => 'rejected']);
        return back()->with('success', 'Annotation rejected.');
    }

    public function destroyAnnotation(Annotation $annotation)
    {
        $annotation->delete();
        return back()->with('success', 'Annotation deleted.');
    }
}
