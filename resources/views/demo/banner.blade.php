@if (config('demo.enabled') && ! request()->cookie('demo_screenshot_mode'))
    <div class="fi-demo-banner" style="height: 2.25rem; box-sizing: border-box; display: flex; align-items: center; justify-content: center; background-color: #f59e0b; color: #1f2937; padding-inline: 1rem; font-size: 0.875rem; font-weight: 600;">
        DEMO režim &mdash; všechna data jsou smyšlená a jednou denně se mažou.
    </div>
@endif
