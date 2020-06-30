<div class="flex flex-col w-full h-screen items-center justify-center bg-gray-200">
    <a href="{{ URL::to( $btnurl ) }}" class="w-64 flex flex-col items-center px-4 py-6 bg-white text-blue-500 no-underline rounded-lg shadow-lg tracking-wide uppercase border border-blue cursor-pointer hover:bg-blue-500 hover:text-white">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus-circle"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
        <span class="mt-2 text-base font-light tracking-tight">{{ $btnlabel }}</span>
        <input type='file' class="hidden" />
    </a>

    
<div class="mt-24">
    <div class="flex flex-col justify-center">
        <div class="text-center flex justify-center text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-coffee">
                <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path>
                <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path>
                <line x1="6" y1="1" x2="6" y2="4"></line>
                <line x1="10" y1="1" x2="10" y2="4"></line>
                <line x1="14" y1="1" x2="14" y2="4"></line>
            </svg>
        </div>

        <div class="w-64 text-gray-700 text-center text-sm">Zatím zde není žádný obsah ale stačí kliknout na tlačítko výše a začít.</div>
    </div>



</div>
</div>
