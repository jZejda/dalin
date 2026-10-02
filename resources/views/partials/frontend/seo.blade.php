{{-- <title>, meta description, canonical, Open Graph / Twitter tags and JSON-LD (ralphjsmit/laravel-seo).
     Controllers may pass `seo` (SEOData or a HasSEO model); otherwise @section('title') is used. --}}
{!! seo(app(\App\Services\Seo\SiteSeo::class)->forView($seo ?? null, $__env->yieldContent('title'))) !!}
