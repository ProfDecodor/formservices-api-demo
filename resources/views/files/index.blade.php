@extends('layouts.app')

@section('title', 'Files')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Files</h1>
        <p class="text-muted mb-0">
            <code>FormServicesClient::api()->files()->findManagedWithHeaders()</code>
        </p>
    </div>
    <a href="{{ route('files.index', array_filter(['application' => $applicationName, 'status' => $workflowStatus])) }}"
       class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-clockwise me-1"></i>Refresh
    </a>
</div>

@if ($error)
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>API Error:</strong> {{ $error }}
    </div>
@else

    {{-- Stats / pagination info --}}
    @if ($pagination)
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center">
                <div class="card-body py-3">
                    <div class="fs-3 fw-bold text-success">{{ number_format($pagination['total']) }}</div>
                    <div class="text-muted small">Total files</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center">
                <div class="card-body py-3">
                    <div class="fs-3 fw-bold text-primary">{{ count($files) }}</div>
                    <div class="text-muted small">On this page</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center">
                <div class="card-body py-3">
                    <div class="fs-3 fw-bold text-info">{{ $pagination['currentPage'] }} / {{ $pagination['totalPages'] }}</div>
                    <div class="text-muted small">Page</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center">
                <div class="card-body py-3">
                    <div class="fw-bold text-secondary font-monospace" style="font-size:.85rem">
                        {{ $pagination['rawHeader'] ?? '—' }}
                    </div>
                    <div class="text-muted small">X-Content-Range</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('files.index') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label class="form-label small text-muted mb-1">Application</label>
                    <select name="application" class="form-select">
                        <option value="">All applications</option>
                        @foreach ($applications as $app)
                            <option value="{{ $app['name'] }}" {{ $applicationName === $app['name'] ? 'selected' : '' }}>
                                {{ $app['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Workflow status</label>
                    <select name="status" class="form-select">
                        <option value="">All statuses</option>
                        @foreach (['START', 'CORRECTION', 'VALIDATION', 'DONE'] as $s)
                            <option value="{{ $s }}" {{ $workflowStatus === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="{{ route('files.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Results count --}}
    @if ($pagination)
    <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="text-muted small">
            Showing items
            <strong>{{ $pagination['start'] }}</strong>–<strong>{{ $pagination['end'] }}</strong>
            of <strong>{{ number_format($pagination['total']) }}</strong>
            @if ($applicationName || $workflowStatus)
                <span class="ms-1">(filtered)</span>
            @endif
        </span>
    </div>
    @endif

    {{-- Table --}}
    @if (count($files) === 0)
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>No files found for the current filters.
        </div>
    @else
        <div class="card mb-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:290px;">UUID</th>
                            <th>Application</th>
                            <th style="width:130px;">Workflow</th>
                            <th style="width:100px;">Status</th>
                            <th style="width:160px;">Step date</th>
                            <th style="width:160px;">Last update</th>
                            <th style="width:60px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($files as $file)
                        <tr>
                            <td class="font-monospace text-muted" style="font-size:.8rem">
                                {{ $file['uuid'] ?? $file['id'] ?? '—' }}
                            </td>
                            <td>
                                <span class="fw-medium">{{ $file['application']['name'] ?? '—' }}</span>
                            </td>
                            <td>
                                @php
                                    $status = $file['workflowStatus'] ?? null;
                                    $badgeClass = match($status) {
                                        'START'      => 'bg-info text-dark',
                                        'CORRECTION' => 'bg-warning text-dark',
                                        'VALIDATION' => 'bg-primary',
                                        'DONE'       => 'bg-success',
                                        default      => 'bg-light text-dark border',
                                    };
                                @endphp
                                @if ($status)
                                    <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            @php $fileStatus = $file['status'] ?? null; @endphp
                            <td>
                                @if ($fileStatus)
                                    <span class="badge {{ $fileStatus === 'OPENED' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $fileStatus }}
                                    </span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td class="text-muted small">
                                {{ isset($file['stepDate']) ? \Carbon\Carbon::parse($file['stepDate'])->format('Y-m-d H:i') : '—' }}
                            </td>
                            <td class="text-muted small">
                                {{ isset($file['lastUpdate']) ? \Carbon\Carbon::parse($file['lastUpdate'])->format('Y-m-d H:i') : '—' }}
                            </td>
                            <td>
                                @if (!empty($file['uuid']))
                                <a href="{{ route('files.show', $file['uuid']) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if ($pagination && $pagination['totalPages'] > 1)
        @php
            $currentQuery = array_filter([
                'application' => $applicationName,
                'status'      => $workflowStatus,
            ]);
        @endphp
        <nav aria-label="Files pagination">
            <ul class="pagination pagination-sm justify-content-center">

                {{-- Previous --}}
                <li class="page-item {{ $pagination['currentPage'] <= 1 ? 'disabled' : '' }}">
                    <a class="page-link"
                       href="{{ route('files.index', array_merge($currentQuery, ['page' => $pagination['currentPage'] - 1])) }}">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>

                {{-- Page numbers (sliding window of 5) --}}
                @php
                    $current = $pagination['currentPage'];
                    $total   = $pagination['totalPages'];
                    $wStart  = max(1, $current - 2);
                    $wEnd    = min($total, $wStart + 4);
                    $wStart  = max(1, $wEnd - 4);
                @endphp

                @if ($wStart > 1)
                    <li class="page-item">
                        <a class="page-link" href="{{ route('files.index', array_merge($currentQuery, ['page' => 1])) }}">1</a>
                    </li>
                    @if ($wStart > 2)
                        <li class="page-item disabled"><span class="page-link">…</span></li>
                    @endif
                @endif

                @for ($p = $wStart; $p <= $wEnd; $p++)
                    <li class="page-item {{ $p === $current ? 'active' : '' }}">
                        <a class="page-link"
                           href="{{ route('files.index', array_merge($currentQuery, ['page' => $p])) }}">
                            {{ $p }}
                        </a>
                    </li>
                @endfor

                @if ($wEnd < $total)
                    @if ($wEnd < $total - 1)
                        <li class="page-item disabled"><span class="page-link">…</span></li>
                    @endif
                    <li class="page-item">
                        <a class="page-link" href="{{ route('files.index', array_merge($currentQuery, ['page' => $total])) }}">{{ $total }}</a>
                    </li>
                @endif

                {{-- Next --}}
                <li class="page-item {{ $pagination['currentPage'] >= $pagination['totalPages'] ? 'disabled' : '' }}">
                    <a class="page-link"
                       href="{{ route('files.index', array_merge($currentQuery, ['page' => $pagination['currentPage'] + 1])) }}">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>

            </ul>
        </nav>
        @endif

    @endif

@endif

@include('partials.code-snippet', [
    'title'     => 'API Call — files()->findManagedWithHeaders()',
    'code'      => $codeSnippet,
    'collapsed' => true,
])

@endsection