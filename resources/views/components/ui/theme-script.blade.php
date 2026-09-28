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
    const isDark = () => preference === 'dark' || (preference === 'system' && media.matches);
    const apply = () => {
        const dark = isDark();
        document.documentElement.classList.toggle('dark', dark);
        document.querySelectorAll('[data-terrain-theme-toggle]').forEach(button => {
            const label = dark ? button.dataset.labelLight : button.dataset.labelDark;
            button.setAttribute('aria-label', label);
            button.title = label;
        });
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
        // Until the first click the theme follows the OS; a click pins the opposite of what is shown.
        document.querySelectorAll('[data-terrain-theme-toggle]').forEach(button => {
            button.addEventListener('click', () => {
                preference = isDark() ? 'light' : 'dark';
                try {
                    localStorage.setItem('color-theme', preference);
                } catch {
                    // Keep the selected theme for this page when storage is unavailable.
                }
                apply();
            });
        });
    }, { once: true });
})();
</script>
