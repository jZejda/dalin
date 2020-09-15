{{-- resources\views\errors\list.blade.php --}}
@if (count($errors) > 0)
    <div class="w-full bg-yellow-200 border-l-4 border-red-500 py-3 px-4">
        <p class="font-bold pb-2">Pozor</p>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif