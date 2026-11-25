<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Program;
use App\Models\Course;
use App\Models\Registration;
use App\Models\User;

class LandingController extends Controller
{
    public function index()
    {
        $stats = [
            'students'      => $this->safeCount(Student::class),
            'programs'      => $this->safeCount(Program::class),
            'courses'       => $this->safeCount(Course::class),
            'registrations' => $this->safeCount(Registration::class),
            'users'         => $this->safeCount(User::class),
        ];

        $programs = $this->safeGet(Program::class, 6);

        return view('landing.index', compact('stats', 'programs'));
    }

    private function safeCount(string $model): int
    {
        try {
            return class_exists($model) ? $model::count() : 0;
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function safeGet(string $model, int $limit)
    {
        try {
            return class_exists($model) ? $model::take($limit)->get() : collect();
        } catch (\Throwable $e) {
            return collect();
        }
    }
}