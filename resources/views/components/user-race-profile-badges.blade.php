@foreach ($profiles as $profile)
    @php
        $class = match ($profile->gender) {
            'H' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
            'D' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        };
    @endphp
    <span class="inline-flex items-center gap-0.5 {{ $size }} font-medium px-2 py-0.5 rounded {{ $class }}">
        <span class="font-semibold">{{ $profile->reg_number }}</span>
        <span class="opacity-50">|</span>
        {{ $profile->first_name }} {{ $profile->last_name }}
    </span>
@endforeach
