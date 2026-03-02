@extends('layouts.app')

@section('title', 'Applications')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Applications</h1>
        <p class="text-muted mb-0">
            <code>FormServicesClient::api()->applications()->findAll()</code>
        </p>
    </div>
    <a href="{{ route('applications.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-clockwise me-1"></i>Refresh
    </a>
</div>

@if ($error)
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>API Error:</strong> {{ $error }}
    </div>
@else

    {{-- Stats row --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center">
                <div class="card-body py-3">
                    <div class="fs-3 fw-bold text-primary">{{ $stats['total'] }}</div>
                    <div class="text-muted small">Total</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center">
                <div class="card-body py-3">
                    <div class="fs-3 fw-bold text-success">{{ $stats['visible'] }}</div>
                    <div class="text-muted small">Visible</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center">
                <div class="card-body py-3">
                    <div class="fs-3 fw-bold text-secondary">{{ $stats['hidden'] }}</div>
                    <div class="text-muted small">Hidden</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center">
                <div class="card-body py-3">
                    <div class="fs-3 fw-bold text-info">{{ $stats['tags'] }}</div>
                    <div class="text-muted small">Tags</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('applications.index') }}" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small text-muted mb-1">Search by name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text"
                               name="search"
                               class="form-control"
                               placeholder="Filter by technical name…"
                               value="{{ $search }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Tag</label>
                    <select name="tag" class="form-select">
                        <option value="">All tags</option>
                        @foreach ($allTags as $tag)
                            <option value="{{ $tag }}" {{ $tagFilter === $tag ? 'selected' : '' }}>
                                {{ $tag }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Visibility</label>
                    <select name="hidden" class="form-select">
                        <option value="1" {{ $showHidden ? 'selected' : '' }}>All (incl. hidden)</option>
                        <option value="0" {{ ! $showHidden ? 'selected' : '' }}>Visible only</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="{{ route('applications.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Results count --}}
    <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="text-muted small">
            Showing <strong>{{ count($filtered) }}</strong> of <strong>{{ $stats['total'] }}</strong> applications
            @if ($search || $tagFilter || ! $showHidden)
                <span class="ms-1">(filtered)</span>
            @endif
        </span>
    </div>

    {{-- Table --}}
    @if (count($filtered) === 0)
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>No applications match the current filters.
        </div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Technical Name</th>
                            <th>Tags</th>
                            <th style="width: 110px;">Visibility</th>
                            <th style="width: 130px;">First Step</th>
                            <th style="width: 80px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($filtered as $app)
                        <tr>
                            <td class="text-muted font-monospace">{{ $app['id'] }}</td>
                            <td>
                                <span class="fw-medium">{{ $app['name'] }}</span>
                            </td>
                            <td>
                                @forelse ($app['tags'] ?? [] as $tag)
                                    <a href="{{ route('applications.index', array_merge(request()->query(), ['tag' => $tag['name']])) }}"
                                       class="badge bg-primary text-decoration-none me-1">
                                        {{ $tag['name'] }}
                                    </a>
                                @empty
                                    <span class="text-muted small">—</span>
                                @endforelse
                            </td>
                            <td>
                                @if ($app['hidden'] ?? false)
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-eye-slash me-1"></i>Hidden
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        <i class="bi bi-eye me-1"></i>Visible
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if ($app['firstStepPrivate'] ?? false)
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-lock me-1"></i>Private
                                    </span>
                                @else
                                    <span class="badge bg-info text-dark">
                                        <i class="bi bi-unlock me-1"></i>Public
                                    </span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('applications.show', $app['id']) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

@endif

@include('partials.code-snippet', [
    'title'     => 'API Call — applications()->findAll()',
    'code'      => $codeSnippet,
    'collapsed' => true,
])

@endsection