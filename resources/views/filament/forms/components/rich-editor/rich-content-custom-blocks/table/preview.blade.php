@php
    $useFirstRowAsHeader = $hasHeader && ! empty($headers);
    $previewRows = array_slice($rows, 0, 3);
    $totalRows = count($rows) + ($useFirstRowAsHeader ? 0 : (! empty($headers) ? 1 : 0));
@endphp

<div class="table-preview">
    @if(! empty($title))
        <h4 class="table-preview-title">{{ $title }}</h4>
    @endif

    <table class="table-preview-table">
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
            @foreach($previewRows as $row)
                <tr>
                    @foreach($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    @if(count($rows) > 3)
        <p class="table-preview-more">… a dalších {{ count($rows) - 3 }} řádků (celkem {{ $totalRows }})</p>
    @endif
</div>
