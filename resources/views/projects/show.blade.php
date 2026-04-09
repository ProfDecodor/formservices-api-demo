@extends('layouts.app')

@section('title', 'Project #' . $id)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item">
                    <a href="{{ route('projects.index') }}">Projects</a>
                </li>
                <li class="breadcrumb-item active">
                    {{ $project['name'] ?? 'Content #' . $id }}
                </li>
            </ol>
        </nav>
        <h1 class="h3 mb-0">
            <i class="bi bi-kanban me-2 text-success"></i>
            {{ $project['name'] ?? 'Project #' . $id }}
        </h1>
    </div>
    <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to list
    </a>
</div>

@if ($error)
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>API Error:</strong> {{ $error }}
    </div>
@else

<div class="row g-4">

    {{-- Project metadata --}}
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Project Details
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted">Content ID</dt>
                    <dd class="col-7">
                        <span class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-25 font-monospace">
                            {{ $project['contentId'] ?? $id }}
                        </span>
                    </dd>

                    <dt class="col-5 text-muted">Legacy ID</dt>
                    <dd class="col-7 font-monospace text-muted small">{{ $project['id'] ?? '—' }}</dd>

                    <dt class="col-5 text-muted">Name</dt>
                    <dd class="col-7 font-monospace">{{ $project['name'] ?? '—' }}</dd>

                    <dt class="col-5 text-muted">Type</dt>
                    <dd class="col-7">
                        <span class="badge bg-light text-dark border font-monospace" style="font-size:.7rem">
                            {{ $project['type'] ?? '—' }}
                        </span>
                    </dd>

                    <dt class="col-5 text-muted">Status</dt>
                    <dd class="col-7">
                        @php
                            $status = $project['status'] ?? null;
                            $statusClass = match(strtoupper($status ?? '')) {
                                'ACTIVE'   => 'success',
                                'ARCHIVED' => 'secondary',
                                'INACTIVE' => 'warning',
                                default    => 'light',
                            };
                        @endphp
                        @if ($status)
                            <span class="badge bg-{{ $statusClass }} {{ $statusClass === 'light' ? 'text-dark border' : '' }}">
                                {{ $status }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </dd>

                    <dt class="col-5 text-muted">Version</dt>
                    <dd class="col-7 font-monospace">{{ $project['version'] ?? '—' }}</dd>

                    @if (!empty($project['description']))
                        <dt class="col-5 text-muted">Description</dt>
                        <dd class="col-7 small">{{ $project['description'] }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    {{-- Files summary --}}
    <div class="col-md-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-files me-2"></i>Project Files</span>
                <span class="badge bg-success">{{ count($files) }}</span>
            </div>
            @if (empty($files))
                <div class="card-body">
                    <p class="text-muted mb-0 fst-italic">No files found in this project.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:40px">ID</th>
                                <th>Filename</th>
                                <th>Type</th>
                                <th style="width:80px">Size</th>
                                <th style="width:140px">Last Update</th>
                                <th style="width:60px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($files as $file)
                                @php
                                    $fileType  = $file['type'] ?? '';
                                    $filename  = $file['filename'] ?? '—';
                                    $fileId    = $file['id'] ?? null;
                                    $ext       = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                    $isText    = ($fileType !== '' && (
                                        str_contains($fileType, 'xml')  ||
                                        str_contains($fileType, 'text') ||
                                        str_contains($fileType, 'json') ||
                                        str_contains($fileType, 'properties')
                                    )) || in_array($ext, ['xml', 'jxml', 'properties', 'json', 'txt', 'html', 'css', 'js']);
                                    $isStartup = $file['startupDocument'] ?? false;
                                    $size      = $file['size'] ?? null;
                                    $sizeStr   = $size !== null
                                        ? ($size >= 1024 ? round($size / 1024, 1) . ' KB' : $size . ' B')
                                        : '—';
                                    $fileUrl   = $fileId ? route('projects.file', [$id, $fileId]) : null;
                                @endphp
                                <tr {{ $fileUrl ? 'style=cursor:pointer onclick=window.location=\'' . $fileUrl . '\'' : '' }}>
                                    <td class="font-monospace text-muted" style="font-size:.75rem">{{ $fileId ?? '—' }}</td>
                                    <td>
                                        @if ($fileUrl)
                                            <a href="{{ $fileUrl }}" class="text-decoration-none font-monospace small fw-semibold">
                                                {{ $filename }}
                                            </a>
                                        @else
                                            <span class="font-monospace small">{{ $filename }}</span>
                                        @endif
                                        @if ($isStartup)
                                            <span class="badge bg-primary ms-1" style="font-size:.6rem" title="Startup document">startup</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-muted border font-monospace" style="font-size:.62rem"
                                              title="{{ $fileType }}">
                                            {{ $ext ?: '?' }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">{{ $sizeStr }}</td>
                                    <td class="text-muted small font-monospace" style="font-size:.72rem">
                                        {{ $file['lastUpdate'] ?? '—' }}
                                    </td>
                                    <td>
                                        @if ($isText)
                                            <a href="{{ $fileUrl }}"
                                               class="badge bg-success bg-opacity-15 text-success border-0 text-decoration-none"
                                               style="font-size:.65rem" title="View content">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-15 text-secondary border-0"
                                                  style="font-size:.65rem" title="Binary file — cannot display">
                                                <i class="bi bi-file-binary"></i>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Raw API response --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-braces me-2"></i>Raw API Response
                <code class="small text-muted ms-2">contents()->find({{ $id }})</code>
            </div>
            <div class="card-body p-0">
                <pre class="mb-0 p-3" style="font-size:.8rem; background:#282c34; color:#abb2bf; border-radius:0 0 .375rem .375rem; max-height:400px; overflow-y:auto;">{{ json_encode($project, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
    </div>

    {{-- Deploy actions — secondary / collapsed --}}
    <div class="col-12">
        <div class="card border-secondary border-opacity-25">
            <div class="card-header bg-transparent text-muted"
                 role="button"
                 data-bs-toggle="collapse"
                 data-bs-target="#deployActions"
                 style="cursor:pointer">
                <i class="bi bi-chevron-right me-1" id="deployChevron"></i>
                <i class="bi bi-lightning me-2"></i>Build &amp; Deploy Actions
                <small class="ms-2 fst-italic">(advanced)</small>
            </div>
            <div class="collapse" id="deployActions">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <p class="fw-semibold small mb-1">
                                <span class="badge bg-warning text-dark me-1">1</span>
                                Prepare for Build
                            </p>
                            <p class="text-muted small mb-2">Writes content to server filesystem.</p>
                            <form method="POST" action="{{ route('projects.prepare', $id) }}">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-sm"
                                        onclick="return confirm('Prepare project #{{ $id }} for build?')">
                                    <i class="bi bi-gear me-1"></i>prepareForBuild({{ $id }})
                                </button>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <p class="fw-semibold small mb-1">
                                <span class="badge bg-danger me-1">2</span>
                                Deploy
                            </p>
                            <p class="text-muted small mb-2">Packages and deploys the WAR file.</p>
                            <form method="POST" action="{{ route('projects.deploy', $id) }}">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Deploy project #{{ $id }}?')">
                                    <i class="bi bi-cloud-upload me-1"></i>deploy({{ $id }})
                                </button>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <p class="fw-semibold small mb-1">
                                <span class="badge bg-info text-dark me-1">3</span>
                                Test
                            </p>
                            <p class="text-muted small mb-2">Opens the deployed form.</p>
                            <a href="{{ route('projects.test', $id) }}" target="_blank"
                               class="btn btn-info btn-sm text-dark">
                                <i class="bi bi-box-arrow-up-right me-1"></i>test({{ $id }})
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endif

@include('partials.code-snippet', [
    'title'     => 'API Call — contents()->find() + files',
    'code'      => $codeSnippet,
    'collapsed' => true,
])

@push('scripts')
<script>
    // Rotate chevron when deploy section expands/collapses
    document.getElementById('deployActions')?.addEventListener('show.bs.collapse', () => {
        document.getElementById('deployChevron')?.classList.replace('bi-chevron-right', 'bi-chevron-down');
    });
    document.getElementById('deployActions')?.addEventListener('hide.bs.collapse', () => {
        document.getElementById('deployChevron')?.classList.replace('bi-chevron-down', 'bi-chevron-right');
    });
</script>
@endpush

@endsection