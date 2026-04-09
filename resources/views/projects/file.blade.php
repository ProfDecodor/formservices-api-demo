@extends('layouts.app')

@section('title', ($file['filename'] ?? 'File') . ' — Project #' . $id)

@section('content')

@php
    $filename = $file['filename'] ?? 'File #' . $fileId;
    $fileType = $file['type'] ?? '';
    $ext      = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $hlLang   = match(true) {
        in_array($ext, ['xml', 'jxml'])    => 'xml',
        $ext === 'json'                     => 'json',
        $ext === 'properties'               => 'properties',
        default                             => 'plaintext',
    };
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item">
                    <a href="{{ route('projects.index') }}">Projects</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('projects.show', $id) }}">Content #{{ $id }}</a>
                </li>
                <li class="breadcrumb-item active font-monospace">{{ $filename }}</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0">
            <i class="bi bi-file-code me-2 text-success"></i>
            <span class="font-monospace">{{ $filename }}</span>
        </h1>
    </div>
    <a href="{{ route('projects.show', $id) }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to project
    </a>
</div>

@if ($error)
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>API Error:</strong> {{ $error }}
    </div>
@else

<div class="row g-4">

    {{-- File metadata --}}
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>File Metadata
            </div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">File ID</dt>
                    <dd class="col-7 font-monospace">{{ $file['id'] ?? '—' }}</dd>

                    <dt class="col-5 text-muted">Filename</dt>
                    <dd class="col-7 font-monospace">{{ $filename }}</dd>

                    <dt class="col-5 text-muted">MIME type</dt>
                    <dd class="col-7 font-monospace" style="font-size:.75rem; word-break:break-all">
                        {{ $fileType ?: '—' }}
                    </dd>

                    <dt class="col-5 text-muted">Extension</dt>
                    <dd class="col-7">
                        <span class="badge bg-light text-dark border font-monospace">{{ $ext ?: '—' }}</span>
                    </dd>

                    <dt class="col-5 text-muted">Size</dt>
                    <dd class="col-7">
                        @php
                            $size = $file['size'] ?? null;
                        @endphp
                        @if ($size !== null)
                            {{ $size >= 1024 ? round($size / 1024, 1) . ' KB' : $size . ' B' }}
                            <span class="text-muted">({{ number_format($size) }} B)</span>
                        @else
                            —
                        @endif
                    </dd>

                    <dt class="col-5 text-muted">Last update</dt>
                    <dd class="col-7 font-monospace" style="font-size:.78rem">
                        {{ $file['lastUpdate'] ?? '—' }}
                    </dd>

                    <dt class="col-5 text-muted">Startup doc</dt>
                    <dd class="col-7">
                        @if ($file['startupDocument'] ?? false)
                            <span class="badge bg-primary">yes</span>
                        @else
                            <span class="text-muted">no</span>
                        @endif
                    </dd>

                    <dt class="col-5 text-muted">Path</dt>
                    <dd class="col-7 font-monospace" style="font-size:.75rem; word-break:break-all">
                        {{ $file['path'] ?? '—' }}
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- File content --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="bi bi-code-slash me-2"></i>File Content
                    <span class="badge bg-secondary ms-1" style="font-size:.65rem">{{ strtoupper($hlLang) }}</span>
                </span>
                @if ($content !== null)
                    <button class="btn btn-sm btn-outline-secondary" id="copyBtn"
                            onclick="copyContent()">
                        <i class="bi bi-clipboard me-1"></i>Copy
                    </button>
                @endif
            </div>
            <div class="card-body p-0">
                @if ($isBinary)
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-file-binary display-6 d-block mb-2"></i>
                        <p class="mb-1">Binary file — content cannot be displayed as text.</p>
                        <small class="font-monospace">{{ $fileType }}</small>
                    </div>
                @elseif ($content === null)
                    <div class="p-4 text-center text-muted fst-italic">
                        No content returned by the API.
                    </div>
                @else
                    <pre id="fileContent" class="mb-0" style="max-height:70vh; overflow-y:auto; border-radius:0 0 .375rem .375rem"><code class="language-{{ $hlLang }}" style="font-size:.78rem">{{ $content }}</code></pre>
                @endif
            </div>
        </div>
    </div>

</div>

@endif

@include('partials.code-snippet', [
    'title'     => 'API Call — contents()->files()->find() + getRaw()',
    'code'      => $codeSnippet,
    'collapsed' => true,
])

@push('scripts')
<script>
    // Load extra highlight.js languages needed for project files
    (function () {
        const langs = ['xml', 'json', 'properties'];
        langs.forEach(lang => {
            const s = document.createElement('script');
            s.src = `https://cdn.jsdelivr.net/npm/highlight.js@11.9.0/lib/languages/${lang}.min.js`;
            s.onload = () => {
                if (window[lang]) hljs.registerLanguage(lang, window[lang]);
                document.querySelectorAll('code.language-' + lang)
                        .forEach(el => hljs.highlightElement(el));
            };
            document.head.appendChild(s);
        });
    })();

    function copyContent() {
        const text = document.getElementById('fileContent')?.innerText ?? '';
        navigator.clipboard.writeText(text).then(() => {
            const btn = document.getElementById('copyBtn');
            btn.innerHTML = '<i class="bi bi-check me-1"></i>Copied';
            setTimeout(() => {
                btn.innerHTML = '<i class="bi bi-clipboard me-1"></i>Copy';
            }, 2000);
        });
    }
</script>
@endpush

@endsection