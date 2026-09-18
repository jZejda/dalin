{{-- Run before styles to avoid flashing the wrong theme. Reuse in Terrain layouts. --}}
<script>
(() => {
    const media = window.matchMedia('(prefers-color-scheme: dark)');
    const read = () => {
        try {
            const value = localStorage.getItem('color-theme');
            return value === 'light' || value === 'dark' ? value : 'system';
        } catch {
            return 'system';
        }
    };
    let preference = read();
    const apply = () => {
        document.documentElement.classList.toggle('dark', preference === 'dark' || (preference === 'system' && media.matches));
        document.querySelectorAll('[data-terrain-theme]').forEach(select => { select.value = preference; });
    };
    apply();
    media.addEventListener('change', apply);
    window.addEventListener('storage', event => {
        if (event.key === 'color-theme' || event.key === null) {
            preference = read();
            apply();
        }
    });
    document.addEventListener('DOMContentLoaded', () => {
        apply();
        document.querySelectorAll('[data-terrain-theme]').forEach(select => {
            select.addEventListener('change', () => {
                preference = select.value;
                try {
                    if (preference === 'system') localStorage.removeItem('color-theme');
                    else localStorage.setItem('color-theme', preference);
                } catch {
                    // Keep the selected theme for this page when storage is unavailable.
                }
                apply();
            });
        });
    }, { once: true });
})();
</script>
