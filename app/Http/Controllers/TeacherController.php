<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(): View
    {
        $teachers = User::where('role', 'teacher')
            ->orderBy('name')
            ->get();

        return view('teachers.index', compact('teachers'));
    }

    public function create(): View
    {
        return view('teachers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'iin' => ['required', 'string', 'regex:/^\d{12}$/', 'unique:users,iin'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:50'],
            'birth_date' => ['nullable', 'date'],
        ]);

        User::create([
            'name' => $validated['name'],
            'iin' => $validated['iin'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'teacher',
            'phone' => $validated['phone'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('teachers.index')->with('status', 'Учитель создан.');
    }

    public function show(User $teacher): View
    {
        abort_unless($teacher->role === 'teacher', 404);

        return view('teachers.show', compact('teacher'));
    }

    public function edit(User $teacher): View
    {
        abort_unless($teacher->role === 'teacher', 404);

        return view('teachers.edit', compact('teacher'));
    }

    public function update(Request $request, User $teacher): RedirectResponse
    {
        abort_unless($teacher->role === 'teacher', 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'iin' => ['required', 'string', 'regex:/^\d{12}$/', 'unique:users,iin,' . $teacher->id],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $teacher->id],
            'phone' => ['nullable', 'string', 'max:50'],
            'birth_date' => ['nullable', 'date'],
        ]);

        $teacher->update([
            'name' => $validated['name'],
            'iin' => $validated['iin'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
        ]);

        return redirect()->route('teachers.index')->with('status', 'Учитель обновлён.');
    }

    public function destroy(User $teacher): RedirectResponse
    {
        abort_unless($teacher->role === 'teacher', 404);

        $teacher->delete();

        return redirect()->route('teachers.index')->with('status', 'Учитель удален.');
    }

    public function editPassword(User $teacher): View
    {
        abort_unless($teacher->role === 'teacher', 404);

        return view('teachers.password', compact('teacher'));
    }

    public function updatePassword(Request $request, User $teacher): RedirectResponse
    {
        abort_unless($teacher->role === 'teacher', 404);

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $teacher->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('teachers.show', $teacher)->with('status', 'Пароль обновлён.');
    }
}
