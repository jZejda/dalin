@vite(['resources/css/app.css'])
<x-filament::widget>
    <x-filament::card>
        {{-- Widget content --}}
        <div class="app-front-content">
            <div class="grid grid-cols-2 gap-4 mb-1">
                <div>
                    <p class="text-gray-500 sm:text-lg dark:text-gray-400">Členský vklad</p>
                    <ul>
                        <li>na účet: <strong>159826453/0600</strong><span class="tracking-tight"> vedený u MONETA Money Bank, a. s.</span> </li>
                        <li>VS použijte: <strong>888</strong> <span class="tracking-tight"> + reg. číslo člena bez ABM</span> </li>
                    </ul>
                </div>
                <div>
                    <p class="text-gray-500 sm:text-lg dark:text-gray-400"></p>
                </div>
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
