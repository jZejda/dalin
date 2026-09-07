@php
    $statePath = $getStatePath();
    $leafletJsSrc = \Filament\Support\Facades\FilamentAsset::getScriptSrc('leaflet');
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        wire:ignore
        x-data="{
            state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
            map: null,
            marker: null,
            init() {
                if (this.map) {
                    return;
                }

                // x-load-js only *starts* the fetch on first use, it does not reliably block
                // this init() until the script has actually finished executing — the first
                // time Leaflet is used on a fresh page, `L` can still be undefined here. So
                // Leaflet is loaded manually with a real load-completion promise instead,
                // shared across every LocationPicker instance on the page.
                (window.__leafletLoading ??= new Promise((resolve, reject) => {
                    if (window.L) {
                        resolve();
                        return;
                    }

                    const script = document.createElement('script');
                    script.src = @js($leafletJsSrc);
                    script.onload = () => resolve();
                    script.onerror = () => reject(new Error('Failed to load Leaflet from ' + script.src));
                    document.head.appendChild(script);
                })).then(() => this.setupMap());
            },
            setupMap() {
                if (this.map) {
                    return;
                }

                const hasValue = Array.isArray(this.state) && this.state[0] !== null && this.state[1] !== null;

                this.map = L.map(this.$refs.mapEl).setView(
                    hasValue ? [this.state[0], this.state[1]] : [49.8175, 15.4730],
                    hasValue ? 13 : 7
                );

                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href=&quot;http://www.openstreetmap.org/copyright&quot;>OpenStreetMap</a>'
                }).addTo(this.map);

                if (hasValue) {
                    this.marker = L.marker([this.state[0], this.state[1]]).addTo(this.map);
                }

                this.map.on('click', (e) => {
                    const lat = Math.round(e.latlng.lat * 1e6) / 1e6;
                    const lon = Math.round(e.latlng.lng * 1e6) / 1e6;

                    if (this.marker) {
                        this.marker.setLatLng([lat, lon]);
                    } else {
                        this.marker = L.marker([lat, lon]).addTo(this.map);
                    }

                    this.state = [lat, lon];
                });

                // The map is very often created while its container is still 0x0 or
                // mid-transition (a Filament modal/panel opening) — Leaflet renders the
                // tile grid based on the container size at creation time, so it needs a
                // nudge once the container settles into its real size.
                new ResizeObserver(() => this.map && this.map.invalidateSize()).observe(this.$refs.mapEl);
            }
        }"
        x-init="init()"
    >
        {{--
            Leaflet gives its container `position: relative` but no z-index, so its internal
            panes/controls (z-index up to 1000 — see leaflet.css) don't get their own stacking
            context and instead compete directly with sibling form fields, painting over any
            positioned dropdown (e.g. the "type" Select below this field) that has a lower or
            unset z-index — regardless of DOM order. Setting z-index here forces a *new* local
            stacking context that caps everything Leaflet draws internally to this one layer, so
            later siblings can stack above the whole map again.
        --}}
        <div x-ref="mapEl" class="rounded-lg" style="width: 100%; height: 320px; position: relative; z-index: 0;"></div>
    </div>
</x-dynamic-component>
