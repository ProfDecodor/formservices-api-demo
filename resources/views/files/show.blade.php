@extends('layouts.app')

@section('title', 'File ' . $uuid)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item">
                    <a href="{{ route('files.index') }}">Files</a>
                </li>
                <li class="breadcrumb-item active font-monospace" style="font-size:.85rem">{{ $uuid }}</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0">
            {{ $file['application']['name'] ?? 'File detail' }}
            <span class="text-muted fs-5 fw-normal ms-2">#{{ $file['id'] ?? '' }}</span>
        </h1>
    </div>
    <a href="{{ route('files.index') }}" class="btn btn-sm btn-outline-secondary">
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

        {{-- File details --}}
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <i class="bi bi-file-earmark me-2"></i>File Details
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">UUID</dt>
                        <dd class="col-7 font-monospace" style="font-size:.8rem; word-break:break-all">
                            {{ $file['uuid'] ?? $uuid }}
                        </dd>

                        <dt class="col-5 text-muted">Application</dt>
                        <dd class="col-7">
                            <span class="fw-medium font-monospace">{{ $file['application']['name'] ?? '—' }}</span>
                        </dd>

                        <dt class="col-5 text-muted">Form</dt>
                        <dd class="col-7 font-monospace">
                            {{ $file['form']['name'] ?? '—' }}
                        </dd>

                        <dt class="col-5 text-muted">Workflow Status</dt>
                        <dd class="col-7">
                            @php
                                $wfStatus = $file['workflowStatus'] ?? null;
                                $wfBadge = match($wfStatus) {
                                    'START'      => 'bg-info text-dark',
                                    'CORRECTION' => 'bg-warning text-dark',
                                    'VALIDATION' => 'bg-primary',
                                    'DONE'       => 'bg-success',
                                    default      => 'bg-light text-dark border',
                                };
                            @endphp
                            @if ($wfStatus)
                                <span class="badge {{ $wfBadge }}">{{ $wfStatus }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </dd>

                        <dt class="col-5 text-muted">Status</dt>
                        <dd class="col-7">
                            @php $fileStatus = $file['status'] ?? null; @endphp
                            @if ($fileStatus)
                                <span class="badge {{ $fileStatus === 'OPENED' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $fileStatus }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </dd>

                        <dt class="col-5 text-muted">Validated</dt>
                        <dd class="col-7">
                            @if ($file['validated'] ?? false)
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Yes</span>
                            @else
                                <span class="badge bg-secondary"><i class="bi bi-x-circle me-1"></i>No</span>
                            @endif
                        </dd>

                        <dt class="col-5 text-muted">Language</dt>
                        <dd class="col-7">{{ $file['language'] ?? '—' }}</dd>

                        <dt class="col-5 text-muted">Order</dt>
                        <dd class="col-7 font-monospace">{{ $file['order'] ?? '—' }}</dd>

                        <dt class="col-5 text-muted">Step date</dt>
                        <dd class="col-7">
                            {{ isset($file['stepDate']) ? \Carbon\Carbon::parse($file['stepDate'])->format('Y-m-d H:i:s') : '—' }}
                        </dd>

                        <dt class="col-5 text-muted">Last update</dt>
                        <dd class="col-7">
                            {{ isset($file['lastUpdate']) ? \Carbon\Carbon::parse($file['lastUpdate'])->format('Y-m-d H:i:s') : '—' }}
                        </dd>

                        @if (isset($file['lastUser']))
                        <dt class="col-5 text-muted">Last user</dt>
                        <dd class="col-7">
                            <span class="fw-medium">{{ $file['lastUser']['fullName'] ?? $file['lastUser']['name'] ?? '—' }}</span>
                            @if (!empty($file['lastUser']['email']))
                                <br><small class="text-muted">{{ $file['lastUser']['email'] }}</small>
                            @endif
                        </dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        {{-- Access permissions --}}
        @if (isset($file['access']))
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <i class="bi bi-shield-check me-2"></i>Your Access Permissions
                </div>
                <div class="card-body">
                    @php
                        $access = $file['access'];
                        $permissions = [
                            'readable'   => ['Read',      'bi-eye'],
                            'updatable'  => ['Update',    'bi-pencil'],
                            'validable'  => ['Validate',  'bi-check-circle'],
                            'deletable'  => ['Delete',    'bi-trash'],
                            'duplicable' => ['Duplicate', 'bi-copy'],
                            'archivable' => ['Archive',   'bi-archive'],
                        ];
                    @endphp
                    <div class="row g-2">
                        @foreach ($permissions as $key => [$label, $icon])
                            <div class="col-6">
                                <div class="d-flex align-items-center gap-2">
                                    @if ($access[$key] ?? false)
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                    @else
                                        <i class="bi bi-x-circle-fill text-danger"></i>
                                    @endif
                                    <span class="small">
                                        <i class="bi {{ $icon }} text-muted me-1"></i>{{ $label }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Accesses list --}}
        @if (!empty($file['accesses']))
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-people me-2"></i>Accesses
                    <span class="badge bg-secondary ms-2">{{ count($file['accesses']) }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th style="width:120px;">Type</th>
                                <th style="width:55px; text-align:center" title="Readable">R</th>
                                <th style="width:55px; text-align:center" title="Updatable">U</th>
                                <th style="width:55px; text-align:center" title="Validable">V</th>
                                <th style="width:55px; text-align:center" title="Deletable">D</th>
                                <th style="width:55px; text-align:center" title="Owner">Owner</th>
                                <th style="width:110px;">Expiry</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($file['accesses'] as $acc)
                            @php
                                $partyType = $acc['partyType'] ?? '';
                                $typeBadge = match($partyType) {
                                    'user'         => 'bg-primary',
                                    'role'         => 'bg-info text-dark',
                                    'role_manager' => 'bg-warning text-dark',
                                    default        => 'bg-secondary',
                                };
                            @endphp
                            <tr>
                                <td class="small">
                                    <span class="font-monospace">{{ $acc['name'] ?? '—' }}</span>
                                    @if (!empty($acc['label']))
                                        <br><small class="text-muted">{{ $acc['label'] }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $typeBadge }} small">{{ $partyType }}</span>
                                </td>
                                @foreach (['readable', 'updatable', 'validable', 'deletable'] as $perm)
                                <td class="text-center">
                                    @if ($acc['accessType'][$perm] ?? false)
                                        <i class="bi bi-check-lg text-success"></i>
                                    @else
                                        <i class="bi bi-dash text-muted"></i>
                                    @endif
                                </td>
                                @endforeach
                                <td class="text-center">
                                    @if ($acc['owner'] ?? false)
                                        <i class="bi bi-star-fill text-warning"></i>
                                    @else
                                        <i class="bi bi-dash text-muted"></i>
                                    @endif
                                </td>
                                <td class="text-muted small">
                                    {{ $acc['expirationDate'] ?? '—' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- Documents --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-text me-1"></i>Documents
                    <span class="badge bg-secondary">{{ count($documents) }}</span>
                </div>

                @if (empty($documents))
                    <div class="card-body text-muted small">
                        No documents linked to this file.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:55px">#</th>
                                    <th>Name</th>
                                    <th style="width:120px">Type</th>
                                    <th style="width:100px">Source</th>
                                    <th>Attached files</th>
                                    <th style="width:140px">Created</th>
                                    <th style="width:80px; text-align:center">Updatable</th>
                                    <th style="width:80px; text-align:center">Accessed</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($documents as $doc)
                                @php
                                    $typeBadge = match($doc['type'] ?? '') {
                                        'ATTACHMENT' => 'bg-primary',
                                        'GENERATED'  => 'bg-info text-dark',
                                        default      => 'bg-secondary',
                                    };
                                    $sourceBadge = match($doc['source'] ?? '') {
                                        'FORM'  => 'bg-success',
                                        'AGENT' => 'bg-warning text-dark',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <tr>
                                    <td class="text-muted small">{{ $doc['id'] ?? '—' }}</td>
                                    <td>
                                        <span class="fw-medium">{{ $doc['name'] ?? '—' }}</span>
                                        <br>
                                        <span class="font-monospace text-muted" style="font-size:.7rem">{{ $doc['uuid'] ?? '' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $typeBadge }} small">{{ $doc['type'] ?? '—' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $sourceBadge }} small">{{ $doc['source'] ?? '—' }}</span>
                                    </td>
                                    <td>
                                        @forelse ($doc['files'] ?? [] as $f)
                                            <div class="small">
                                                <i class="bi bi-paperclip text-muted me-1"></i>
                                                {{ $f['fileName'] ?? '?' }}
                                                <span class="text-muted ms-1">({{ number_format(($f['size'] ?? 0) / 1024, 1) }} KB)</span>
                                                <span class="badge bg-light text-dark border ms-1" style="font-size:.65rem">{{ $f['type'] ?? '' }}</span>
                                            </div>
                                        @empty
                                            <span class="text-muted small">—</span>
                                        @endforelse
                                    </td>
                                    <td class="small text-muted">
                                        {{ isset($doc['creationDate']) ? \Carbon\Carbon::parse($doc['creationDate'])->format('Y-m-d H:i') : '—' }}
                                    </td>
                                    <td class="text-center">
                                        @if ($doc['updatable'] ?? false)
                                            <i class="bi bi-check-lg text-success"></i>
                                        @else
                                            <i class="bi bi-dash text-muted"></i>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($doc['accessed'] ?? false)
                                            <i class="bi bi-check-lg text-success"></i>
                                        @else
                                            <i class="bi bi-dash text-muted"></i>
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
                </div>
                <div class="card-body p-0">
                    <pre class="mb-0 p-3" style="font-size:.8rem; background:#282c34; color:#abb2bf; border-radius:0 0 .375rem .375rem; max-height:500px; overflow-y:auto;">{{ json_encode($file, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
        </div>

    </div>

@endif

@include('partials.code-snippet', [
    'title'     => 'API Call — files()->find()',
    'code'      => $codeSnippet,
    'collapsed' => true,
])

@endsection