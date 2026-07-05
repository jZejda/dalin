@if (config('demo.enabled'))
    <div style="border: 1px solid #f59e0b; background-color: rgba(245, 158, 11, 0.1); border-radius: 0.5rem; padding: 0.75rem 1rem; font-size: 0.875rem; line-height: 1.6;">
        <strong>Demo přihlašovací údaje</strong><br>
        Správce: <code>admin@demo.cz</code><br>
        Člen: <code>member@demo.cz</code><br>
        Heslo (oba účty): <code>{{ config('demo.admin_password') }}</code>
    </div>
@endif
