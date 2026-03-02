@extends('layouts.app')

@section('title', 'Application #' . $id)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item">
                    <a href="{{ route('applications.index') }}">Applications</a>
                </li>
                <li class="breadcrumb-item active">#{{ $id }}</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0">
            {{ $application['name'] ?? 'Application #' . $id }}
        </h1>
    </div>
    <a href="{{ route('applications.index') }}" class="btn btn-sm btn-outline-secondary">
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

        {{-- Application details --}}
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <i class="bi bi-info-circle me-2"></i>Application Details
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">ID</dt>
                        <dd class="col-7 font-monospace">{{ $application['id'] ?? '—' }}</dd>

                        <dt class="col-5 text-muted">Technical Name</dt>
                        <dd class="col-7 font-monospace">{{ $application['name'] ?? '—' }}</dd>

                        <dt class="col-5 text-muted">Visibility</dt>
                        <dd class="col-7">
                            @if ($application['hidden'] ?? false)
                                <span class="badge bg-secondary"><i class="bi bi-eye-slash me-1"></i>Hidden</span>
                            @else
                                <span class="badge bg-success"><i class="bi bi-eye me-1"></i>Visible</span>
                            @endif
                        </dd>

                        <dt class="col-5 text-muted">First Step</dt>
                        <dd class="col-7">
                            @if ($application['firstStepPrivate'] ?? false)
                                <span class="badge bg-warning text-dark"><i class="bi bi-lock me-1"></i>Private</span>
                            @else
                                <span class="badge bg-info text-dark"><i class="bi bi-unlock me-1"></i>Public</span>
                            @endif
                        </dd>

                        <dt class="col-5 text-muted">First Step Form ID</dt>
                        <dd class="col-7 font-monospace">{{ $application['firstStepForm']['id'] ?? '—' }}</dd>

                        <dt class="col-5 text-muted">Tags</dt>
                        <dd class="col-7">
                            @forelse ($application['tags'] ?? [] as $tag)
                                <a href="{{ route('applications.index', ['tag' => $tag['name']]) }}"
                                   class="badge bg-primary text-decoration-none me-1">
                                    {{ $tag['name'] }}
                                </a>
                            @empty
                                <span class="text-muted">—</span>
                            @endforelse
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Public access type --}}
        @if (isset($application['publicAccessType']))
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <i class="bi bi-shield me-2"></i>Public Access Permissions
                </div>
                <div class="card-body">
                    @php
                        $access = $application['publicAccessType'];
                        $permissions = [
                            'readable'    => ['Read',      'bi-eye'],
                            'updatable'   => ['Update',    'bi-pencil'],
                            'validable'   => ['Validate',  'bi-check-circle'],
                            'deletable'   => ['Delete',    'bi-trash'],
                            'duplicable'  => ['Duplicate', 'bi-copy'],
                            'archivable'  => ['Archive',   'bi-archive'],
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

        {{-- Labels & Texts --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-translate me-2"></i>Labels &amp; Texts
                </div>
                <div class="card-body">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 18%">Scope</th>
                                <th style="width: 22%">Field</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ([
                                'Application' => [
                                    'nameLabel'        => $application['nameLabel']        ?? null,
                                    'descriptionLabel' => $application['descriptionLabel'] ?? null,
                                ],
                            ] + collect($application['tags'] ?? [])
                                ->mapWithKeys(fn($tag) => [
                                    'Tag ' . $tag['name'] => [
                                        'nameLabel'        => $tag['nameLabel']        ?? null,
                                        'descriptionLabel' => $tag['descriptionLabel'] ?? null,
                                    ]
                                ])->all()
                            as $scope => $fields)
                                @php $rowspan = count(array_filter($fields)) * 2; @endphp
                                @foreach ($fields as $fieldName => $labelObj)
                                    @if ($labelObj)
                                        <tr>
                                            @if ($loop->first)
                                                <td class="text-muted align-middle" rowspan="{{ $rowspan }}">
                                                    @if (str_starts_with($scope, 'Tag '))
                                                        Tag <span class="badge bg-primary">{{ substr($scope, 4) }}</span>
                                                    @else
                                                        {{ $scope }}
                                                    @endif
                                                </td>
                                            @endif
                                            <td class="font-monospace small align-middle" rowspan="2">{{ $fieldName }}</td>
                                            <td class="font-monospace small text-muted align-middle">
                                                <span class="badge bg-light text-secondary border">key</span>
                                                {{ $labelObj['label'] ?? '—' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="small align-middle">
                                                @if (!empty($labelObj['texts']))
                                                    @foreach ($labelObj['texts'] as $locale => $text)
                                                        <span class="badge bg-secondary me-1">{{ $locale }}</span>{{ $text }}<br>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted fst-italic">no text available</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Metadata / Datastores --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-database me-2"></i>Datastores Metadata</span>
                    <code class="small text-muted">applications()->getMetadata({{ $id }})</code>
                </div>
                <div class="card-body">
                    @if (empty($metadata))
                        <p class="text-muted mb-0">No metadata available for this application.</p>
                    @else
                        <pre class="bg-dark text-light p-3 rounded mb-0" style="font-size: .8rem; max-height: 400px; overflow-y: auto;">{{ json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    @endif
                </div>
            </div>
        </div>

        {{-- Raw API response --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-braces me-2"></i>Raw API Response
                </div>
                <div class="card-body p-0">
                    <pre class="mb-0 p-3" style="font-size: .8rem; background: #282c34; color: #abb2bf; border-radius: 0 0 .375rem .375rem; max-height: 400px; overflow-y: auto;">{{ json_encode($application, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
        </div>

    </div>

@endif

@include('partials.code-snippet', [
    'title'     => 'API Call — applications()->find() + getMetadata()',
    'code'      => $codeSnippet,
    'collapsed' => true,
])

@endsection