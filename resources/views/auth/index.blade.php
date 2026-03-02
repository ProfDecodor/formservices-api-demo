@extends('layouts.app')

@section('title', 'Auth — Authenticated User')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-person-circle me-2"></i>Authenticated User</h1>
</div>

@if ($error)
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>API Error:</strong> {{ $error }}
    </div>
@else

@php
    $account        = $me['account']        ?? [];
    $authorizations = $me['authorizations'] ?? [];
    $language       = $me['language']       ?? null;
    $languages      = $me['languages']      ?? [];
    $searchFields   = $me['searchFields']   ?? [];
    $services       = $me['services']       ?? [];

    // Group authorizations by prefix
    $authGroups = [];
    foreach ($authorizations as $auth) {
        $prefix = explode('_', $auth)[0];
        $authGroups[$prefix][] = $auth;
    }
    ksort($authGroups);

    $prefixBadge = [
        'ADMIN'  => 'bg-secondary',
        'FILE'   => 'bg-primary',
        'MANAGE' => 'bg-warning text-dark',
        'STUDIO' => 'bg-info text-dark',
    ];
@endphp

<div class="row g-4">

    {{-- Account card --}}
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-person me-2"></i>Account
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted">ID</dt>
                    <dd class="col-7 font-monospace">{{ $account['id'] ?? '—' }}</dd>

                    <dt class="col-5 text-muted">Login</dt>
                    <dd class="col-7 font-monospace fw-medium">{{ $account['name'] ?? '—' }}</dd>

                    <dt class="col-5 text-muted">Full name</dt>
                    <dd class="col-7">{{ $account['fullName'] ?? '—' }}</dd>

                    <dt class="col-5 text-muted">Email</dt>
                    <dd class="col-7">
                        <a href="mailto:{{ $account['email'] ?? '' }}" class="text-decoration-none">
                            {{ $account['email'] ?? '—' }}
                        </a>
                    </dd>

                    <dt class="col-5 text-muted">Status</dt>
                    <dd class="col-7">
                        @php $status = $account['status'] ?? null; @endphp
                        @if ($status === 1)
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Active</span>
                        @elseif ($status !== null)
                            <span class="badge bg-danger">{{ $status }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </dd>

                    <dt class="col-5 text-muted">Connections</dt>
                    <dd class="col-7">{{ $account['connectionCount'] ?? '—' }}</dd>

                    <dt class="col-5 text-muted">Language</dt>
                    <dd class="col-7">
                        <span class="badge bg-light text-dark border">{{ strtoupper($language ?? '—') }}</span>
                        @if (isset($languages[$language]))
                            <span class="text-muted small ms-1">{{ $languages[$language] }}</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Context card --}}
    <div class="col-md-7">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-sliders me-2"></i>UI Context
            </div>
            <div class="card-body">
                <div class="row g-3">

                    {{-- Services --}}
                    <div class="col-12">
                        <p class="text-muted small mb-1 text-uppercase fw-semibold" style="font-size:.7rem; letter-spacing:.05em">Services</p>
                        @forelse ($services as $key => $value)
                            <span class="badge {{ $value === 'true' || $value === true ? 'bg-success' : 'bg-secondary' }} me-1">
                                {{ $key }}: {{ $value }}
                            </span>
                        @empty
                            <span class="text-muted small">None</span>
                        @endforelse
                    </div>

                    {{-- Languages available --}}
                    <div class="col-12">
                        <p class="text-muted small mb-1 text-uppercase fw-semibold" style="font-size:.7rem; letter-spacing:.05em">Available Languages</p>
                        @forelse ($languages as $code => $label)
                            <span class="badge bg-light text-dark border me-1">{{ strtoupper($code) }} — {{ $label }}</span>
                        @empty
                            <span class="text-muted small">None</span>
                        @endforelse
                    </div>

                    {{-- Counts --}}
                    <div class="col-12">
                        <p class="text-muted small mb-2 text-uppercase fw-semibold" style="font-size:.7rem; letter-spacing:.05em">Response Counts</p>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ([
                                ['applications', 'bi-grid',           'primary'],
                                ['groups',       'bi-people',          'secondary'],
                                ['searchFields', 'bi-search',          'info'],
                                ['texts',        'bi-translate',       'warning'],
                                ['authorizations','bi-shield-check',   'success'],
                            ] as [$key, $icon, $color])
                            <div class="border rounded px-3 py-2 text-center" style="min-width:100px">
                                <div class="fw-bold fs-5 text-{{ $color }}">{{ count($me[$key] ?? []) }}</div>
                                <div class="small text-muted"><i class="bi {{ $icon }} me-1"></i>{{ ucfirst($key) }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Account creation --}}
                    <div class="col-12">
                        <p class="text-muted small mb-1 text-uppercase fw-semibold" style="font-size:.7rem; letter-spacing:.05em">Account Creation Allowed</p>
                        @if ($me['accountCreationAllowed'] ?? false)
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Yes</span>
                        @else
                            <span class="badge bg-secondary"><i class="bi bi-x-circle me-1"></i>No</span>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Authorizations --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-shield-check me-1"></i>Authorizations
                <span class="badge bg-secondary">{{ count($authorizations) }}</span>
            </div>
            <div class="card-body">
                @if (empty($authorizations))
                    <span class="text-muted small">None</span>
                @else
                    @foreach ($authGroups as $prefix => $perms)
                    @php $badge = $prefixBadge[$prefix] ?? 'bg-secondary'; @endphp
                    <div class="mb-3">
                        <p class="text-muted small mb-2 text-uppercase fw-semibold" style="font-size:.7rem; letter-spacing:.05em">
                            {{ $prefix }}
                            <span class="badge bg-light text-dark border ms-1">{{ count($perms) }}</span>
                        </p>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach ($perms as $perm)
                                <span class="badge {{ $badge }} small">{{ $perm }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- Search Fields --}}
    @if (!empty($searchFields))
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-search me-2"></i>Search Fields
                <span class="badge bg-secondary ms-2">{{ count($searchFields) }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px">#</th>
                            <th>Field name</th>
                            <th style="width:90px">Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($searchFields as $sf)
                        <tr>
                            <td class="text-muted small">{{ $sf['id'] ?? '—' }}</td>
                            <td class="font-monospace small">{{ $sf['fieldName'] ?? '—' }}</td>
                            <td>
                                <span class="badge bg-light text-dark border small">{{ $sf['type'] ?? '—' }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Raw API response (texts + applications omitted) --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-braces me-2"></i>Raw API Response
                <span class="text-muted small ms-2">(keys <code>texts</code> and <code>applications</code> omitted — too large)</span>
            </div>
            <div class="card-body p-0">
                <pre class="mb-0 p-3" style="font-size:.8rem; background:#282c34; color:#abb2bf; border-radius:0 0 .375rem .375rem; max-height:500px; overflow-y:auto;">{{ json_encode($meForDisplay, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
    </div>

</div>

@endif

@include('partials.code-snippet', [
    'title'     => 'API Call — auth()->me()',
    'code'      => $codeSnippet,
    'collapsed' => true,
])

@endsection
