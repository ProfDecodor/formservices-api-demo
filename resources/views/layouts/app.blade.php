<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'FormServices API Demo') — FormServices Demo</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Highlight.js (syntax highlighting) -->
    <link href="https://cdn.jsdelivr.net/npm/highlight.js@11.9.0/styles/atom-one-dark.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: #212529;
        }

        .sidebar .nav-link {
            color: #adb5bd;
            padding: .5rem 1rem;
            border-radius: .375rem;
            margin-bottom: 2px;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background-color: #343a40;
        }

        .sidebar .nav-link.active {
            color: #fff;
            background-color: #0d6efd;
        }

        .sidebar .nav-link i {
            width: 1.25rem;
            text-align: center;
        }

        .sidebar-heading {
            font-size: .65rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #6c757d;
            padding: .75rem 1rem .25rem;
        }

        .main-content {
            min-height: calc(100vh - 56px);
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: -.01em;
        }

        .badge-version {
            font-size: .65rem;
            vertical-align: middle;
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- Top navbar -->
    <nav class="navbar navbar-dark bg-dark border-bottom border-secondary">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-gear-wide-connected me-2"></i>FormServices
                <span class="badge bg-primary badge-version ms-1">API Demo</span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-secondary small">
                    <i class="bi bi-box me-1"></i>profdecodor/formservices-api-client
                </span>
                <a href="https://github.com/profdecodor/formservices-api-client"
                   target="_blank"
                   class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-github me-1"></i>GitHub
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">

            <!-- Sidebar navigation -->
            <nav class="col-md-2 sidebar py-3 px-2">

                <div class="sidebar-heading">Navigation</div>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                           href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </a>
                    </li>
                </ul>

                <div class="sidebar-heading">API Resources</div>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('applications.*') ? 'active' : '' }}"
                           href="{{ route('applications.index') }}">
                            <i class="bi bi-grid me-2"></i>Applications
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('files.*') ? 'active' : '' }}"
                           href="{{ route('files.index') }}">
                            <i class="bi bi-file-earmark me-2"></i>Files
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('auth.index') ? 'active' : '' }}"
                           href="{{ route('auth.index') }}">
                            <i class="bi bi-person-circle me-2"></i>Auth
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-secondary disabled">
                            <i class="bi bi-paperclip me-2"></i>Attachments
                            <span class="badge bg-secondary ms-auto float-end" style="font-size:.6rem">soon</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-secondary disabled">
                            <i class="bi bi-file-pdf me-2"></i>Documents
                            <span class="badge bg-secondary ms-auto float-end" style="font-size:.6rem">soon</span>
                        </a>
                    </li>
                </ul>

                <div class="sidebar-heading">Studio</div>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item">
                        <a class="nav-link text-secondary disabled">
                            <i class="bi bi-code-square me-2"></i>Contents
                            <span class="badge bg-secondary ms-auto float-end" style="font-size:.6rem">soon</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}"
                           href="{{ route('projects.index') }}">
                            <i class="bi bi-kanban me-2"></i>Projects
                        </a>
                    </li>
                </ul>

            </nav>

            <!-- Main content area -->
            <main class="col-md-10 main-content py-4 px-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>

        </div>
    </div>

    <!-- Bootstrap 5 JS bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Highlight.js -->
    <script src="https://cdn.jsdelivr.net/npm/highlight.js@11.9.0/lib/core.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/highlight.js@11.9.0/lib/languages/php.min.js"></script>
    <script>
        hljs.registerLanguage('php', window['php']);
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('code.language-php').forEach(el => hljs.highlightElement(el));
        });
    </script>

    @stack('scripts')
</body>
</html>
