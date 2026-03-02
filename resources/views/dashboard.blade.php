@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Dashboard</h1>
        <p class="text-muted mb-0">FormServices API Client — Demo & Testing Application</p>
    </div>
</div>

<!-- Package info banner -->
<div class="card border-primary mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-auto">
                <i class="bi bi-box-seam text-primary" style="font-size: 2.5rem;"></i>
            </div>
            <div class="col">
                <h5 class="card-title mb-1">
                    <code>profdecodor/formservices-api-client</code>
                </h5>
                <p class="card-text text-muted mb-0">
                    A Laravel package for interacting with the JWay FormServices API.
                    Supports multi-client configuration and API versioning.
                </p>
            </div>
            <div class="col-auto">
                <span class="badge bg-warning text-dark">
                    <i class="bi bi-exclamation-triangle me-1"></i>Early Development
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Feature cards -->
<h5 class="text-muted text-uppercase mb-3" style="font-size: .75rem; letter-spacing: .08em;">Package Features</h5>
<div class="row g-3 mb-4">

    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-people text-primary me-2 fs-5"></i>
                    <h6 class="card-title mb-0">Multi-client Support</h6>
                </div>
                <p class="card-text text-muted small">
                    Configure multiple API clients for different environments (dev, staging, prod).
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-layers text-success me-2 fs-5"></i>
                    <h6 class="card-title mb-0">API Versioning</h6>
                </div>
                <p class="card-text text-muted small">
                    Built-in support for API version management (currently v2023).
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-file-earmark-code text-info me-2 fs-5"></i>
                    <h6 class="card-title mb-0">Typed Resources</h6>
                </div>
                <p class="card-text text-muted small">
                    Full IDE autocompletion with typed resource classes.
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-arrow-left-right text-warning me-2 fs-5"></i>
                    <h6 class="card-title mb-0">Pagination Handling</h6>
                </div>
                <p class="card-text text-muted small">
                    Built-in support for paginated responses via <code>X-Content-Range</code> headers.
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-lightning text-danger me-2 fs-5"></i>
                    <h6 class="card-title mb-0">Laravel Integration</h6>
                </div>
                <p class="card-text text-muted small">
                    Service Provider, Facade, and dependency injection support out of the box.
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-collection text-secondary me-2 fs-5"></i>
                    <h6 class="card-title mb-0">Comprehensive Coverage</h6>
                </div>
                <p class="card-text text-muted small">
                    13+ API resources with 40+ methods covering the full FormServices API.
                </p>
            </div>
        </div>
    </div>

</div>

<!-- Available resources -->
<h5 class="text-muted text-uppercase mb-3" style="font-size: .75rem; letter-spacing: .08em;">Available Resources</h5>
<div class="row g-3 mb-4">

    @foreach ($resources as $resource)
    <div class="col-md-3">
        <div class="card h-100 {{ $resource['available'] ? '' : 'opacity-50' }}">
            <div class="card-body d-flex align-items-start gap-3">
                <i class="bi {{ $resource['icon'] }} fs-4 text-{{ $resource['color'] }} mt-1"></i>
                <div>
                    <h6 class="mb-1">{{ $resource['name'] }}</h6>
                    <p class="text-muted small mb-2">{{ $resource['description'] }}</p>
                    @if ($resource['available'])
                        <a href="{{ $resource['route'] }}" class="btn btn-sm btn-outline-primary">
                            View demo <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    @else
                        <span class="badge bg-secondary">Coming soon</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach

</div>

<!-- Configuration status -->
<h5 class="text-muted text-uppercase mb-3" style="font-size: .75rem; letter-spacing: .08em;">Configuration</h5>
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            @foreach ($clients as $name => $client)
            <div class="col-md-6">
                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                    <div>
                        <div class="fw-semibold">
                            <i class="bi bi-server me-2 text-muted"></i>
                            Client: <code>{{ $name }}</code>
                            @if ($name === $defaultClient)
                                <span class="badge bg-primary ms-2" style="font-size:.65rem">default</span>
                            @endif
                        </div>
                        <div class="text-muted small mt-1">
                            URL: <span class="font-monospace">{{ $client['url'] ?: '(not set)' }}</span>
                        </div>
                        <div class="text-muted small">
                            Login: <span class="font-monospace">{{ $client['login'] ?: '(not set)' }}</span>
                            &nbsp;·&nbsp;
                            Version: <span class="font-monospace">{{ $client['version'] ?: '(not set)' }}</span>
                        </div>
                    </div>
                    <div>
                        @if ($client['configured'])
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle me-1"></i>Configured
                            </span>
                        @else
                            <span class="badge bg-danger">
                                <i class="bi bi-x-circle me-1"></i>Not configured
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if (! $anyClientConfigured)
        <div class="alert alert-warning mt-3 mb-0">
            <i class="bi bi-exclamation-triangle me-2"></i>
            No API client is configured. Add <code>FORMSERVICES_*</code> variables to your <code>.env</code> file to enable API demos.
        </div>
        @endif
    </div>
</div>

<!-- Quick start snippet -->
<h5 class="text-muted text-uppercase mb-3" style="font-size: .75rem; letter-spacing: .08em;">Quick Start</h5>
<div class="card">
    <div class="card-body">
        <pre class="mb-0 p-3 bg-dark text-light rounded" style="font-size: .85rem;"><code>use Jway\FormServicesApiClient\Facades\FormServicesClient;

// Get the default API instance
$api = FormServicesClient::api();

// List all applications
$applications = $api->applications()->findAll();

// List files with filters
$files = $api->files()->findManaged([
    'application.id' => 9,
    'workflowStatus' => 'DONE',
    'max' => 10,
]);</code></pre>
    </div>
</div>

@endsection
