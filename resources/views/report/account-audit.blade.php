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
                <th style="font-weight: bold">Servicio</th>
                <th style="font-weight: bold">Hora</th>
                <th style="font-weight: bold">Día</th>
                <th style="font-weight: bold">Mesero</th>
                <th style="font-weight: bold">Venta</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                <th>{{$row->clave}}</th>
                <th>{{$row->ordenid}}</th>
                <th>{{$row->servicio}}</th>
                <th>{{$row->dia}}</th>
                <th>{{$row->hora}}</th>
                <th>{{$row->mesero}}</th>
                <th>${{number_format($row->venta)}}</th>
            </tr>
            @php
                $count++;
                $totalSales = $totalSales+$row->venta;
            @endphp
            @endforeach
            @if($count > 0)
            <tr >
                <th></th>
                <th style="font-weight: bold" colspan="3">{{ $count }} Cuentas</th>
                <th style="font-weight: bold" colspan="3">Total Cuentas  ${{ number_format($totalSales) }}</th>
            </tr>
            @endif
            <tr></tr>
        </tbody>
    </table>
</body>
</html>
