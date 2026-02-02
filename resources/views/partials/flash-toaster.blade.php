@php
$flash = [
    'success' => session('success'),
    'error' => session('error'),
    'errors' => $errors->any() ? $errors->all() : [],
];
$hasFlash = $flash['success'] || $flash['error'] || !empty($flash['errors']);
@endphp

<div id="toaster-container" class="toaster-container" aria-live="polite"></div>

<style>
.toaster-container {
    position: fixed;
    top: 1rem;
    right: 1rem;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    max-width: 380px;
    pointer-events: none;
}
.toaster-container .toast-item {
    pointer-events: auto;
    padding: 0.875rem 1rem;
    border-radius: 0.5rem;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    animation: toastSlideIn 0.3s ease;
    font-size: 0.9375rem;
    line-height: 1.4;
}
.toaster-container .toast-item.toast-success {
    background: #0f766e;
    color: #fff;
    border: none;
}
.toaster-container .toast-item.toast-error {
    background: #b91c1c;
    color: #fff;
    border: none;
}
.toaster-container .toast-item.toast-warning {
    background: #b45309;
    color: #fff;
    border: none;
}
[data-theme="dark"] .toaster-container .toast-item.toast-success { background: #0d9488; }
[data-theme="dark"] .toaster-container .toast-item.toast-error { background: #dc2626; }
[data-theme="dark"] .toaster-container .toast-item.toast-warning { background: #d97706; }
.toaster-container .toast-item .toast-icon { flex-shrink: 0; font-size: 1.25rem; margin-top: 0.05rem; }
.toaster-container .toast-item .toast-body { flex: 1; }
.toaster-container .toast-item .toast-body ul { margin: 0.25rem 0 0 0; padding-left: 1.25rem; }
.toaster-container .toast-item .toast-close {
    background: none;
    border: none;
    color: inherit;
    opacity: 0.85;
    cursor: pointer;
    padding: 0.15rem;
    line-height: 1;
    font-size: 1.1rem;
}
.toaster-container .toast-item .toast-close:hover { opacity: 1; }
@keyframes toastSlideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
.toaster-container .toast-item.toast-hide {
    animation: toastSlideOut 0.25s ease forwards;
}
@keyframes toastSlideOut {
    to { transform: translateX(100%); opacity: 0; }
}
@media (max-width: 480px) {
    .toaster-container { left: 0.75rem; right: 0.75rem; max-width: none; }
}
</style>

@if($hasFlash)
<script>
(function() {
    window.__flashToaster = @json($flash);
})();
</script>
@endif

<script>
(function() {
    function showToasts() {
        var data = window.__flashToaster;
        if (!data) return;
        var container = document.getElementById('toaster-container');
        if (!container) return;
        var duration = 5000;

        function addToast(type, html, icon) {
            var el = document.createElement('div');
            el.className = 'toast-item toast-' + type;
            el.setAttribute('role', 'alert');
            el.innerHTML = '<span class="toast-icon">' + icon + '</span>' +
                '<div class="toast-body">' + html + '</div>' +
                '<button type="button" class="toast-close" aria-label="Close">&times;</button>';
            container.appendChild(el);

            function remove() {
                el.classList.add('toast-hide');
                setTimeout(function() { el.remove(); }, 260);
            }

            el.querySelector('.toast-close').addEventListener('click', remove);
            var t = setTimeout(remove, duration);
            el.addEventListener('mouseenter', function() { clearTimeout(t); });
            el.addEventListener('mouseleave', function() { t = setTimeout(remove, 2000); });
        }

        if (data.success) {
            addToast('success', escapeHtml(data.success), '<i class="bi bi-check-circle-fill"></i>');
        }
        if (data.error) {
            addToast('error', escapeHtml(data.error), '<i class="bi bi-x-circle-fill"></i>');
        }
        if (data.errors && data.errors.length) {
            var list = '<ul>' + data.errors.map(function(e) { return '<li>' + escapeHtml(e) + '</li>'; }).join('') + '</ul>';
            addToast('error', list, '<i class="bi bi-exclamation-triangle-fill"></i>');
        }

        window.__flashToaster = null;
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', showToasts);
    } else {
        showToasts();
    }
})();
</script>
