<?php

namespace App\Http\Controllers;

use App\Jobs\LookupGameMediaJob;
use App\Models\Annotation;
use App\Models\Dunk;
use App\Models\Memory;
use App\Models\Mixtape;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function showLogin()
    {
        if (auth()->check()) {
            return redirect('/');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return back()->withErrors(['email' => 'Those credentials don\'t match.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect('/')->with('success', 'Admin mode on.');
    }

    public function exit(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Admin mode off.');
    }

    public function index()
    {
        $pending = Memory::pending()->with('tags', 'reports')->latest()->paginate(20, ['*'], 'page');
        $pendingAnnotations = Annotation::pending()->with('memory')->latest()->paginate(20, ['*'], 'annotations_page');
        $pendingMixtapes = Mixtape::pending()->withCount(['memories', 'dunks'])->latest()->paginate(20, ['*'], 'mixtapes_page');
        $pendingDunks = Dunk::pending()->latest()->paginate(20, ['*'], 'dunks_page');

        return view('admin.index', compact('pending', 'pendingAnnotations', 'pendingMixtapes', 'pendingDunks'));
    }

    public function approve(Memory $memory)
    {
        $memory->update(['status' => 'approved']);

        if ($memory->game_date && !$memory->gameMedia) {
            LookupGameMediaJob::dispatch($memory)->afterResponse();
        }

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

    public function approveMixtape(Mixtape $mixtape)
    {
        $mixtape->update(['status' => 'approved']);
        return back()->with('success', 'Mix tape approved.');
    }

    public function rejectMixtape(Mixtape $mixtape)
    {
        $mixtape->update(['status' => 'rejected']);
        return back()->with('success', 'Mix tape rejected.');
    }

    public function approveDunk(Dunk $dunk)
    {
        $dunk->update(['status' => 'approved']);
        return back()->with('success', 'Dunk approved.');
    }

    public function rejectDunk(Dunk $dunk)
    {
        $dunk->update(['status' => 'rejected']);
        return back()->with('success', 'Dunk rejected.');
    }

    public function destroyDunk(Dunk $dunk)
    {
        $dunk->delete();
        return back()->with('success', 'Dunk deleted.');
    }
}
