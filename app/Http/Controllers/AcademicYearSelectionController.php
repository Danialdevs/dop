<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AcademicYearSelectionController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
        ]);

        session(['academic_year_id' => (int) $validated['academic_year_id']]);

        /** @var AcademicYear|null $academicYear */
        $academicYear = AcademicYear::find($validated['academic_year_id']);
        $message = $academicYear
            ? sprintf('Учебный год переключён на %s.', $academicYear->name)
            : 'Учебный год обновлён.';

        return back()->with('status', $message);
    }
}
