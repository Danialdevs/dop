<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        $students = User::where('role', 'student')
            ->orderBy('name')
            ->get();

        return view('students.index', compact('students'));
    }

    public function create(): View
    {
        return view('students.create', [
            'orderTypes' => $this->orderTypes(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'iin' => ['required', 'string', 'regex:/^\d{12}$/', 'unique:users,iin'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'birth_date' => ['nullable', 'date'],
            'enrollment_order_number' => ['required', 'string', 'max:255'],
            'enrollment_order_date' => ['required', 'date'],
            'enrollment_order_type' => ['required', 'string', 'in:' . implode(',', array_keys($this->orderTypes()))],
        ]);

        $temporaryPassword = $this->generatePassword();

        $student = User::create([
            'name' => $validated['name'],
            'iin' => $validated['iin'],
            'email' => $validated['email'],
            'password' => Hash::make($temporaryPassword),
            'role' => 'student',
            'phone' => $validated['phone'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'is_active' => true,
        ]);

        $student->orders()->create([
            'order_number' => $validated['enrollment_order_number'],
            'order_date' => $validated['enrollment_order_date'],
            'order_type' => $validated['enrollment_order_type'],
        ]);

        return redirect()->route('students.index')->with('status', "Ученик создан. Временный пароль: {$temporaryPassword}");
    }

    public function show(User $student): View
    {
        abort_unless($student->role === 'student', 404);

        $student->load('orders');

        return view('students.show', [
            'student' => $student,
            'orderTypes' => $this->orderTypes(),
            'order' => $student->orders->sortByDesc('order_date')->first(),
        ]);
    }

    public function edit(User $student): View
    {
        abort_unless($student->role === 'student', 404);

        $order = $student->orders()->orderByDesc('order_date')->first();

        return view('students.edit', [
            'student' => $student,
            'orderTypes' => $this->orderTypes(),
            'order' => $order,
        ]);
    }

    public function update(Request $request, User $student): RedirectResponse
    {
        abort_unless($student->role === 'student', 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'iin' => ['required', 'string', 'regex:/^\d{12}$/', 'unique:users,iin,' . $student->id],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $student->id],
            'phone' => ['nullable', 'string', 'max:50'],
            'birth_date' => ['nullable', 'date'],
            'enrollment_order_number' => ['required', 'string', 'max:255'],
            'enrollment_order_date' => ['required', 'date'],
            'enrollment_order_type' => ['required', 'string', 'in:' . implode(',', array_keys($this->orderTypes()))],
        ]);

        $student->update([
            'name' => $validated['name'],
            'iin' => $validated['iin'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
        ]);

        $order = $student->orders()->orderByDesc('order_date')->first();

        if ($order) {
            $order->update([
                'order_number' => $validated['enrollment_order_number'],
                'order_date' => $validated['enrollment_order_date'],
                'order_type' => $validated['enrollment_order_type'],
            ]);
        } else {
            $student->orders()->create([
                'order_number' => $validated['enrollment_order_number'],
                'order_date' => $validated['enrollment_order_date'],
                'order_type' => $validated['enrollment_order_type'],
            ]);
        }

        return redirect()->route('students.index')->with('status', 'Ученик обновлён.');
    }

    public function editPassword(User $student): View
    {
        abort_unless($student->role === 'student', 404);

        return view('students.password', compact('student'));
    }

    public function updatePassword(Request $request, User $student): RedirectResponse
    {
        abort_unless($student->role === 'student', 404);

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $student->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('students.show', $student)->with('status', 'Пароль обновлён.');
    }

    public function destroy(User $student): RedirectResponse
    {
        abort_unless($student->role === 'student', 404);

        $student->delete();

        return redirect()->route('students.index')->with('status', 'Ученик удален.');
    }

    private function orderTypes(): array
    {
        return [
            'enrollment' => 'Приказ о зачислении в ОО',
            'group_formation' => 'Приказ о формировании группы',
        ];
    }

    private function generatePassword(int $length = 12): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%^&*';
        $password = '';
        $maxIndex = strlen($alphabet) - 1;

        for ($i = 0; $i < $length; $i++) {
            $password .= $alphabet[random_int(0, $maxIndex)];
        }

        return $password;
    }
}
