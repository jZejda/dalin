@php
    $useFirstRowAsHeader = $hasHeader && ! empty($headers);
@endphp

<div class="table-block @if($striped) table-block-striped @endif @if($compact) table-block-compact @endif">
    @if(! empty($title))
        <p class="table-block-title">{{ $title }}</p>
    @endif

    <div class="table-block-scroll">
        <table class="table-block-table">
            @if($useFirstRowAsHeader)
                <thead>
                    <tr>
                        @foreach($headers as $header)
                            <th>{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
            @endif
            <tbody>
                @if(! $useFirstRowAsHeader && ! empty($headers))
                    <tr>
                        @foreach($headers as $cell)
                            <td>{{ $cell }}</td>
                        @endforeach
                    </tr>
                @endif
                @foreach($rows as $row)
                    <tr>
                        @foreach($row as $cell)
                            <td>{{ $cell }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
