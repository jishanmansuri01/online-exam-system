@extends('layouts.student')

@section('title', 'Taking Exam')
@section('page-title', $exam->title)

@push('styles')
<style>
    .timer-box {
        position: sticky;
        top: 20px;
        z-index: 10;
    }
    .timer-display {
        font-size: 2.5rem;
        font-weight: 700;
        font-family: monospace;
        letter-spacing: 2px;
    }
    .timer-danger { color: #dc3545 !important; animation: blink 1s infinite; }
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.4} }
    .question-card { border-left: 4px solid #0d6efd; }
    .question-card.answered { border-left-color: #198754; }
    .option-label {
        cursor: pointer;
        padding: 10px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        display: block;
        transition: all 0.2s;
        margin-bottom: 8px;
    }
    .option-label:hover { border-color: #0d6efd; background: #f0f5ff; }
    input[type="radio"]:checked + .option-label {
        border-color: #198754;
        background: #f0fff4;
        color: #198754;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<form id="exam-form" action="{{ route('student.exams.submit', $exam) }}" method="POST">
    @csrf

    <div class="row g-4">

        <!-- Timer & Info Sidebar -->
        <div class="col-md-3">
            <div class="card timer-box text-center">
                <div class="card-body p-4">
                    <div class="text-muted small mb-1">Time Remaining</div>
                    <div class="timer-display text-primary" id="timer">00:00:00</div>
                    <hr>
                    <div class="text-muted small">{{ $exam->title }}</div>
                    <div class="mt-2">
                        <span class="badge bg-info">{{ $questions->count() }} Questions</span>
                        <span class="badge bg-warning text-dark">{{ $exam->total_marks }} Marks</span>
                    </div>
                    <hr>
                    <button type="button" class="btn btn-success w-100"
                            onclick="confirmSubmit()">
                        <i class="bi bi-check-circle me-1"></i> Submit Exam
                    </button>
                </div>
            </div>
        </div>

        <!-- Questions -->
        <div class="col-md-9">
            @foreach($questions as $index => $question)
            <div class="card mb-3 question-card" id="qcard-{{ $question->id }}">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold">Q{{ $index + 1 }}. {{ $question->question_text }}</span>
                        <span class="badge bg-secondary">{{ $question->marks }} mark(s)</span>
                    </div>

                    @if($question->question_type === 'mcq')
                        @foreach($question->options as $option)
                        <div>
                            <input type="radio"
                                   name="answers[{{ $question->id }}]"
                                   id="opt-{{ $option->id }}"
                                   value="{{ $option->id }}"
                                   class="d-none mcq-input"
                                   data-qid="{{ $question->id }}"
                                   onchange="markAnswered({{ $question->id }})">
                            <label for="opt-{{ $option->id }}" class="option-label">
                                {{ $option->option_text }}
                            </label>
                        </div>
                        @endforeach

                    @else
                        <textarea name="answers[{{ $question->id }}]"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Write your answer here..."
                                  onkeyup="markAnswered({{ $question->id }})"></textarea>
                    @endif
                </div>
            </div>
            @endforeach

            <div class="text-end mt-3">
                <button type="button" class="btn btn-success btn-lg px-5"
                        onclick="confirmSubmit()">
                    <i class="bi bi-check-circle me-2"></i> Submit Exam
                </button>
            </div>
        </div>
    </div>
</form>

<!-- Auto submit modal -->
<div class="modal fade" id="timeupModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-alarm me-2"></i>Time's Up!</h5>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bi bi-hourglass-bottom fs-1 text-danger"></i>
                <p class="mt-3 mb-0">Your time has expired. The exam is being submitted automatically...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let timeLeft = {{ (int) $timeLeft }};

    function pad(n) { return n < 10 ? '0' + n : n; }

    function updateTimer() {
        const h = Math.floor(timeLeft / 3600);
        const m = Math.floor((timeLeft % 3600) / 60);
        const s = timeLeft % 60;
        const display = pad(h) + ':' + pad(m) + ':' + pad(s);
        $('#timer').text(display);

        if (timeLeft <= 60) {
            $('#timer').addClass('timer-danger').removeClass('text-primary');
        }

        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            $('#timeupModal').modal('show');
            setTimeout(function() {
                $('#exam-form').submit();
            }, 3000);
            return;
        }
        timeLeft--;
    }

    const timerInterval = setInterval(updateTimer, 1000);
    updateTimer();

    function markAnswered(qid) {
        $('#qcard-' + qid).addClass('answered');
    }

    function confirmSubmit() {
        if (confirm('Are you sure you want to submit the exam?')) {
            clearInterval(timerInterval);
            $('#exam-form').submit();
        }
    }

    // Prevent accidental navigation
    window.onbeforeunload = function() {
        return 'Are you sure you want to leave? Your exam progress may be lost!';
    };

    $('#exam-form').on('submit', function() {
        window.onbeforeunload = null;
    });
</script>
@endpush