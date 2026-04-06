@if($rows->count())
<table class="table table-sm">
    <thead>
        <tr>
            @foreach(array_keys((array)$rows->first() ?? []) as $h)
                <th>{{ $h }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
            <tr>
                @foreach((array)$row as $col)
                    <td>{{ $col }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
@else
<p>Nenhum resultado</p>
@endif