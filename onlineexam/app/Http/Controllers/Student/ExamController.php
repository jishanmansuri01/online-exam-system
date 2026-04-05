<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\StudentExamAttempt;
use App\Models\StudentAnswer;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::where('status', 'active')
                     ->withCount('questions')
                     ->get();

        $attemptedExamIds = StudentExamAttempt::where('user_id', Auth::id())
                            ->where('status', 'submitted')
                            ->pluck('exam_id')
                            ->toArray();

        return view('student.exams.index', compact('exams', 'attemptedExamIds'));
    }

    public function start(Exam $exam)
    {
        $alreadyAttempted = StudentExamAttempt::where('user_id', Auth::id())
                            ->where('exam_id', $exam->id)
                            ->where('status', 'submitted')
                            ->exists();

        if ($alreadyAttempted) {
            return redirect()->route('student.exams.index')
                             ->with('error', 'You have already attempted this exam!');
        }

        return view('student.exams.start', compact('exam'));
    }

    public function take(Exam $exam)
    {
        $alreadyAttempted = StudentExamAttempt::where('user_id', Auth::id())
                            ->where('exam_id', $exam->id)
                            ->where('status', 'submitted')
                            ->exists();

        if ($alreadyAttempted) {
            return redirect()->route('student.exams.index')
                             ->with('error', 'You have already attempted this exam!');
        }

        $attempt = StudentExamAttempt::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'exam_id' => $exam->id,
                'status'  => 'in_progress',
            ],
            [
                'started_at' => now(),
            ]
        );

        $questions = $exam->questions()->with('options')->get();
        $timeLeft  = $exam->duration * 60;

        if ($attempt->started_at) {
            $elapsed  = now()->diffInSeconds($attempt->started_at);
            $timeLeft = max(0, ($exam->duration * 60) - $elapsed);
        }

        return view('student.exams.take', compact('exam', 'attempt', 'questions', 'timeLeft'));
    }

    public function submit(Request $request, Exam $exam)
    {
        $attempt = StudentExamAttempt::where('user_id', Auth::id())
                   ->where('exam_id', $exam->id)
                   ->where('status', 'in_progress')
                   ->firstOrFail();

        if ($request->answers) {
            foreach ($request->answers as $questionId => $answer) {
                $existing = StudentAnswer::where('attempt_id', $attempt->id)
                            ->where('question_id', $questionId)
                            ->first();

                $question = $exam->questions()->find($questionId);

                if ($question->question_type === 'mcq') {
                    if ($existing) {
                        $existing->update(['option_id' => $answer]);
                    } else {
                        StudentAnswer::create([
                            'attempt_id'  => $attempt->id,
                            'question_id' => $questionId,
                            'option_id'   => $answer,
                        ]);
                    }
                } else {
                    if ($existing) {
                        $existing->update(['answer_text' => $answer]);
                    } else {
                        StudentAnswer::create([
                            'attempt_id'  => $attempt->id,
                            'question_id' => $questionId,
                            'answer_text' => $answer,
                        ]);
                    }
                }
            }
        }

        $obtainedMarks = 0;
        foreach ($exam->questions as $question) {
            if ($question->question_type === 'mcq') {
                $studentAnswer = StudentAnswer::where('attempt_id', $attempt->id)
                                 ->where('question_id', $question->id)
                                 ->first();

                if ($studentAnswer && $studentAnswer->option_id) {
                    $correctOption = $question->options()
                                     ->where('is_correct', true)
                                     ->first();
                    if ($correctOption && $studentAnswer->option_id == $correctOption->id) {
                        $obtainedMarks += $question->marks;
                    }
                }
            }
        }

        $percentage = ($exam->total_marks > 0)
                      ? round(($obtainedMarks / $exam->total_marks) * 100, 2)
                      : 0;

        $status = $obtainedMarks >= $exam->pass_marks ? 'pass' : 'fail';

        Result::create([
            'user_id'        => Auth::id(),
            'exam_id'        => $exam->id,
            'attempt_id'     => $attempt->id,
            'total_marks'    => $exam->total_marks,
            'obtained_marks' => $obtainedMarks,
            'percentage'     => $percentage,
            'status'         => $status,
        ]);

        $attempt->update([
            'status'       => 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('student.results.index')
                         ->with('success', 'Exam submitted successfully!');
    }
}