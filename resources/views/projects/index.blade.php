@extends('layouts.app')

@section('title', 'Projects')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i class="bi bi-kanban me-2 text-success"></i>Projects</h1>
        <p class="text-muted mb-0 small mt-1">
            Studio — list and manage projects via <code>projects()->findAll()</code>
        </p>
    </div>
    <span class="badge bg-success fs-6">{{ count($projects) }} project(s)</span>
</div>

{{-- Search filter --}}
<div class="card mb-4">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('projects.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text"
                       name="search"
                       class="form-control form-control-sm"
                       placeholder="Filter by name…"
                       value="{{ $search }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-success">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
                @if ($search)
                    <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-secondary ms-1">
                        <i class="bi bi-x me-1"></i>Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

@if ($error)
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>API Error:</strong> {{ $error }}
    </div>
@elseif (empty($projects))
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        No projects found{{ $search ? ' matching "' . e($search) . '"' : '' }}.
    </div>
@else

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-table me-2"></i>Studio Projects</span>
            <small class="text-muted">
                Click a row to view build info and deploy actions
            </small>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th style="width:70px">ID</th>
                        <th style="width:90px">Content ID</th>
                        <th>Name</th>
                        <th style="width:110px">Type</th>
                        <th style="width:110px">Status</th>
                        <th style="width:90px">Version</th>
                        <th style="width:60px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($projects as $project)
                        @php
                            $contentId = $project['contentId'] ?? $project['id'] ?? null;
                            $status    = $project['status'] ?? null;
                            $statusClass = match(strtoupper($status ?? '')) {
                                'ACTIVE'    => 'success',
                                'ARCHIVED'  => 'secondary',
                                'INACTIVE'  => 'warning',
                                default     => 'light',
                            };
                        @endphp
                        <tr style="cursor:pointer"
                            onclick="window.location='{{ route('projects.show', $contentId) }}'">
                            <td class="font-monospace text-muted small">{{ $project['id'] ?? '—' }}</td>
                            <td class="font-monospace small">
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                    {{ $contentId ?? '—' }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-semibold">{{ $project['name'] ?? '—' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace" style="font-size:.7rem">
                                    {{ $project['type'] ?? '—' }}
                                </span>
                            </td>
                            <td>
                                @if ($status)
                                    <span class="badge bg-{{ $statusClass }} {{ $statusClass === 'light' ? 'text-dark border' : '' }}">
                                        {{ $status }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="font-monospace small text-muted">{{ $project['version'] ?? '—' }}</td>
                            <td>
                                <a href="{{ route('projects.show', $contentId) }}"
                                   class="btn btn-sm btn-outline-success"
                                   onclick="event.stopPropagation()">
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endif

@include('partials.code-snippet', [
    'title'     => 'API Call — projects()->findAll()',
    'code'      => $codeSnippet,
    'collapsed' => true,
])

@endsection