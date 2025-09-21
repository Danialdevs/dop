<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(Request $request): View
    {
        $gradeSystems = collect($this->availableGradeSystems());
        $school = School::orderBy('id')->first();
        $defaultSelection = $gradeSystems->pluck('key')->toArray();

        if (! $school) {
            $school = new School([
                'is_active' => true,
            ]);
            $school->allowed_grade_systems = $defaultSelection;
        }

        $selectedSystems = collect($school->allowed_grade_systems ?? $defaultSelection);

        return view('settings.index', [
            'school' => $school,
            'gradeSystems' => $gradeSystems,
            'selectedSystems' => $selectedSystems,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $gradeSystems = $this->availableGradeSystems();
        $gradeKeys = collect($gradeSystems)->pluck('key')->implode(',');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'grade_systems' => ['nullable', 'array'],
            'grade_systems.*' => ['string', 'in:' . $gradeKeys],
        ]);

        $school = School::orderBy('id')->first();
        if (! $school) {
            $school = new School();
            $school->is_active = true;
        }

        $school->name = $validated['name'];
        $school->allowed_grade_systems = array_values($validated['grade_systems'] ?? []);
        $school->save();

        return redirect()
            ->route('settings.edit')
            ->with('status', 'Настройки обновлены.');
    }

    private function availableGradeSystems(): array
    {
        return [
            [
                'key' => 'five_point',
                'label' => '5-балльная система',
                'description' => 'Оценки от 1 до 5. Лучшая оценка 5. Можно ставить плюсы и минусы. Можно ставить зачёт и незачёт.',
                'scale' => ['ЗЧ', 'НЗ', '1', '2', '3', '4', '5', '5-', '5+'],
            ],
            [
                'key' => 'american',
                'label' => 'Американская система',
                'description' => 'Оценки A, B, C, D, F. Лучшая оценка A. Можно ставить плюсы и минусы. Можно ставить зачёт и незачёт.',
                'scale' => ['ЗЧ', 'НЗ', 'F', 'D', 'C', 'B', 'A', 'A-', 'A+'],
            ],
            [
                'key' => 'ten_point',
                'label' => '10-балльная система',
                'description' => 'Оценки от 1 до 10. Лучшая оценка 10. Можно ставить зачёт и незачёт.',
                'scale' => ['ЗЧ', 'НЗ', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10'],
            ],
        ];
    }
}
