@extends('layouts.admin')

@section('title', 'All Results')
@section('page-title', 'All Student Results')

@section('content')
<div class="card">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold">All Results</h6>
        <div class="d-flex gap-2">
            <select id="examFilter" class="form-select form-select-sm" style="width:200px">
                <option value="">All Exams</option>
                @foreach($exams as $exam)
                    <option value="{{ $exam->id }}">{{ $exam->title }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0" id="resultsTable">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>Email</th>
                    <th>Exam</th>
                    <th>Total Marks</th>
                    <th>Obtained</th>
                    <th>Percentage</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($results as $result)
                <tr data-exam="{{ $result->exam_id }}">
                    <td>{{ $loop->iteration }}</td>
                    <td class="fw-semibold">{{ $result->user->name }}</td>
                    <td class="text-muted">{{ $result->user->email }}</td>
                    <td>{{ $result->exam->title }}</td>
                    <td>{{ $result->total_marks }}</td>
                    <td>{{ $result->obtained_marks }}</td>
                    <td>{{ $result->percentage }}%</td>
                    <td>
                        @if($result->status === 'pass')
                            <span class="badge bg-success">Pass</span>
                        @else
                            <span class="badge bg-danger">Fail</span>
                        @endif
                    </td>
                    <td>{{ $result->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('admin.results.download', $result) }}"
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-download me-1"></i> PDF
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">
                        No results found yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$('#examFilter').on('change', function() {
    const examId = $(this).val();
    $('#resultsTable tbody tr').each(function() {
        if (examId === '' || $(this).data('exam') == examId) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
});
</script>
@endpush