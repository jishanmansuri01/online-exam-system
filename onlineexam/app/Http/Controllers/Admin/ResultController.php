<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Result;
use App\Models\Exam;
use Barryvdh\DomPDF\Facade\Pdf;

class ResultController extends Controller
{
    public function index()
    {
        $results = Result::with(['user', 'exam'])
                         ->latest()
                         ->get();

        $exams = Exam::all();

        return view('admin.results.index', compact('results', 'exams'));
    }

    public function download(Result $result)
    {
        $result->load('exam', 'user');

        $pdf = Pdf::loadView('student.results.download', compact('result'));

        return $pdf->download('result-' . $result->user->name . '-' . $result->exam->title . '.pdf');
    }
}