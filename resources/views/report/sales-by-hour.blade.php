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
                <th style="font-weight: bold">Hora</th>
                <th style="font-weight: bold">Cantidad</th>
                <th style="font-weight: bold">Venta $</th>
                <th style="font-weight: bold">Venta %</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                <th>{{$row->hora}}</th>
                <th>{{$row->cantidad}}</th>
                <th>{{(($row->venta))}}</th>
                <th>{{($row->venta_porcentaje)}}</th>
            </tr>
            @endforeach
            @if(count($data) > 0)
            <tr >
                <th style="font-weight: bold;text-align:right;"  >Totales</th>
                <th style="font-weight: bold;" >{{ $data[0]->count_sales}}</th>
                <th style="font-weight: bold" >{{ ($data[0]->total_sales) }}</th>
                <th style="font-weight: bold" >{{ "100.00%" }}</th>
            </tr>
            @endif
            <tr></tr>
        </tbody>
    </table>
</body>
</html>
