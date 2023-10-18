<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    @php
    $count = 0;
    $totalSales = 0;
    @endphp
    <table>
        <thead>
            <tr>
                <th style="font-weight: bold">Orden</th>
                <th style="font-weight: bold">Cliente</th>
                <th style="font-weight: bold">Teléfono</th>
                <th style="font-weight: bold">Fecha Compra</th>
                <th style="font-weight: bold">Calle</th>
                <th style="font-weight: bold">Número</th>
                <th style="font-weight: bold">Colonia</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                <th>{{ $row->orden}}</th>
                <th>{{ $row->cliente}}</th>
                <th>{{ $row->telefono}}</th>
                <th>{{ $row->fecha_compra }}</th>
                <th>{{ $row->calle }}</th>
                <th>{{ $row->numero }}</th>
                <th>{{ $row->colonia }}</th>
            </tr>
            @endforeach
            <tr></tr>
        </tbody>
    </table>
</body>
</html>
