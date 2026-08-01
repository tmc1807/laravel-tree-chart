<div class="side-box">
    <div class="side-title">{{ $title }}</div>
    <table class="side-table">
        <thead>
        <tr>
            <th>#</th>
            <th>Nama Indikator</th>
            <th>Sat</th>
            @for($y = $tahunAwal ?? 2025; $y <= ($tahunAkhir ?? 2030); $y++)
                <th>{{ $y }}</th>
            @endfor
        </tr>
        </thead>
        <tbody>
        @foreach($rows as $row)
            <tr>
                <td class="num">{{ $loop->iteration }}</td>
                @foreach($row as $cell)
                    <td>{{ $cell }}</td>
                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
