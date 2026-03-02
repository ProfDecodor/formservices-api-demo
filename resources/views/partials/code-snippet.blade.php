{{--
    Reusable code snippet partial.

    Usage:
        @include('partials.code-snippet', [
            'code'        => $codeSnippet,        // required — PHP string
            'title'       => 'API Call',           // optional — card header title
            'collapsed'   => false,                // optional — start collapsed
        ])
--}}

@php
    $snippetTitle    = $title ?? 'API Call';
    $snippetId       = 'snippet-' . Str::random(6);
    $startCollapsed  = $collapsed ?? false;
@endphp

<div class="card mt-4 border-secondary">
    <div class="card-header d-flex justify-content-between align-items-center bg-dark text-light py-2"
         role="button"
         data-bs-toggle="collapse"
         data-bs-target="#{{ $snippetId }}"
         aria-expanded="{{ $startCollapsed ? 'false' : 'true' }}">
        <span class="small fw-semibold">
            <i class="bi bi-code-slash me-2 text-info"></i>{{ $snippetTitle }}
        </span>
        <div class="d-flex align-items-center gap-2">
            <button type="button"
                    class="btn btn-sm btn-outline-secondary border-0 copy-btn py-0 px-2"
                    data-snippet="{{ $snippetId }}"
                    title="Copy to clipboard"
                    onclick="event.stopPropagation()">
                <i class="bi bi-clipboard"></i>
            </button>
            <i class="bi bi-chevron-down toggle-icon" style="font-size: .75rem; transition: transform .2s;"></i>
        </div>
    </div>
    <div id="{{ $snippetId }}" class="{{ $startCollapsed ? 'collapse' : 'collapse show' }}">
        <div class="card-body p-0">
            <pre class="mb-0" style="border-radius: 0 0 .375rem .375rem; margin: 0;"><code class="language-php" id="{{ $snippetId }}-code">{{ $code }}</code></pre>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
    // Rotate chevron on collapse toggle
    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(trigger => {
        const target = document.querySelector(trigger.dataset.bsTarget);
        if (!target) return;
        const icon = trigger.querySelector('.toggle-icon');
        if (!icon) return;
        target.addEventListener('show.bs.collapse', () => icon.style.transform = 'rotate(180deg)');
        target.addEventListener('hide.bs.collapse', () => icon.style.transform = 'rotate(0deg)');
        // Set initial state
        if (target.classList.contains('show')) icon.style.transform = 'rotate(180deg)';
    });

    // Copy to clipboard
    document.querySelectorAll('.copy-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const code = document.getElementById(btn.dataset.snippet + '-code');
            if (!code) return;
            navigator.clipboard.writeText(code.innerText).then(() => {
                const icon = btn.querySelector('i');
                icon.className = 'bi bi-clipboard-check text-success';
                setTimeout(() => icon.className = 'bi bi-clipboard', 2000);
            });
        });
    });
</script>
@endpush
@endonce