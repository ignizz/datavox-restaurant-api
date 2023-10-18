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
                <th style="font-weight: bold">Descripción</th>
                <th style="font-weight: bold">Cant</th>
                <th style="font-weight: bold">Venta $</th>
                <th style="font-weight: bold">Venta %</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                @php
                $count = $count + $row->cuantos;
                $totalSales = $totalSales+floatval($row->venta);
            @endphp
            @endforeach
            @foreach($data as $row)
            @php
                $totalSalesPercent = $row->venta > 0? (floatval($row->venta) /floatval($totalSales))* 100: "0.00"
            @endphp
            <tr>
                <th>{{$row->servicio}}</th>
                <th>{{$row->cuantos}}</th>
                <th>{{(($row->venta))}}</th>
                <th>{{($totalSalesPercent)}}</th>
            </tr>
            @endforeach
            @if($count > 0)
            <tr >
                <th style="font-weight: bold;text-align:right;"  >Totales</th>
                <th style="font-weight: bold;" >{{ $count}}</th>
                <th style="font-weight: bold" >{{ ($totalSales) }}</th>
                <th style="font-weight: bold" >{{ "100.00%" }}</th>
            </tr>
            @endif
            <tr></tr>
        </tbody>
    </table>
</body>
</html>
