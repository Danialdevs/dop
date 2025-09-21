<?php

namespace App\Providers;

use App\Models\AcademicYear;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        setlocale(LC_TIME, 'ru_RU.UTF-8');
        Carbon::setLocale('ru');

        if (Schema::hasTable('academic_years')) {
            View::composer('*', function ($view) {
                $academicYears = AcademicYear::orderByDesc('start_date')->get();

                if ($academicYears->isEmpty()) {
                    $view->with('academicYears', collect())
                        ->with('currentAcademicYear', null);

                    return;
                }

                $selectedYearId = Session::get('academic_year_id');
                $selectedYear = $academicYears->firstWhere('id', $selectedYearId);

                if (! $selectedYear) {
                    $selectedYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();
                    Session::put('academic_year_id', $selectedYear?->id);
                }

                $view->with('academicYears', $academicYears)
                    ->with('currentAcademicYear', $selectedYear);
            });
        }
    }
}
