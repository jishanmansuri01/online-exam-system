@extends('layouts.admin')

@section('title', 'Add Question')
@section('page-title', 'Add Question')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="mb-3">
            <a href="{{ route('admin.questions.index', $exam) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Questions
            </a>
        </div>
        <div class="card">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold">New Question for: {{ $exam->title }}</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.questions.store', $exam) }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Question Text <span class="text-danger">*</span></label>
                        <textarea name="question_text" rows="3"
                                  class="form-control @error('question_text') is-invalid @enderror"
                                  placeholder="Enter your question here...">{{ old('question_text') }}</textarea>
                        @error('question_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Question Type <span class="text-danger">*</span></label>
                            <select name="question_type" id="question_type" class="form-select" onchange="toggleOptions()">
                                <option value="mcq" {{ old('question_type') == 'mcq' ? 'selected' : '' }}>MCQ</option>
                                <option value="short_answer" {{ old('question_type') == 'short_answer' ? 'selected' : '' }}>Short Answer</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Marks <span class="text-danger">*</span></label>
                            <input type="number" name="marks"
                                   class="form-control @error('marks') is-invalid @enderror"
                                   value="{{ old('marks', 1) }}" min="1">
                            @error('marks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div id="mcq-options">
                        <label class="form-label fw-semibold">Options <span class="text-danger">*</span></label>
                        <p class="text-muted small">Select the radio button next to the correct answer.</p>
                        @for($i = 0; $i < 4; $i++)
                        <div class="input-group mb-2">
                            <div class="input-group-text">
                                <input type="radio" name="correct_option" value="{{ $i }}"
                                       {{ old('correct_option') == $i ? 'checked' : ($i == 0 ? 'checked' : '') }}>
                            </div>
                            <input type="text" name="options[]"
                                   class="form-control"
                                   placeholder="Option {{ $i + 1 }}"
                                   value="{{ old('options.'.$i) }}">
                        </div>
                        @endfor
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> Add Question
                        </button>
                        <a href="{{ route('admin.questions.index', $exam) }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleOptions() {
    const type = $('#question_type').val();
    if (type === 'mcq') {
        $('#mcq-options').show();
    } else {
        $('#mcq-options').hide();
    }
}
toggleOptions();
</script>
@endpush