<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th colspan="7" style="font-weight: bold">Ordenes Con Descuento</th>
            </tr>
            <tr>
                <th style="font-weight: bold">Servicio</th>
                <th style="font-weight: bold">Hora</th>
                <th style="font-weight: bold">Día</th>
                <th style="font-weight: bold">Precio</th>
                <th style="font-weight: bold">Autorizó</th>
                <th style="font-weight: bold">Orden</th>
                <th style="font-weight: bold">Observación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data->orders_discount as $orderDiscount)
            <tr>
                <th>{{$orderDiscount->servicio}}</th>
                <th>{{$orderDiscount->hora}}</th>
                <th>{{$orderDiscount->dia}}</th>
                <th>{{ str_replace("-", "", $orderDiscount->importe) }}</th>
                <th>{{ $orderDiscount->autorizo }}</th>
                <th>{{ $orderDiscount->clave }}</th>
                <th>{{ $orderDiscount->observaciones }}</th>
            </tr>
            @endforeach
        </tbody>
    </table>
    <table>
        <thead>
            <tr>
                <th colspan="7" style="font-weight: bold">Ordenes Canceladas</th>
            </tr>
            <tr>
                <th style="font-weight: bold">Servicio</th>
                <th style="font-weight: bold">Día</th>
                <th style="font-weight: bold">Hora</th>
                <th style="font-weight: bold">H/C</th>
                <th style="font-weight: bold">Precio</th>
                <th style="font-weight: bold">Autorizó</th>
                <th style="font-weight: bold">Orden</th>
                <th style="font-weight: bold">Observación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data->orders_cancel as $orderDiscount)
            <tr>
                <th>{{$orderDiscount->servicio}}</th>
                <th>{{$orderDiscount->dia}}</th>
                <th>{{$orderDiscount->hora}}</th>
                <th>{{$orderDiscount->hc}}</th>
                <th>{{ str_replace("-", "", $orderDiscount->importe) }}</th>
                <th>{{ $orderDiscount->autorizo }}</th>
                <th>{{ $orderDiscount->clave }}</th>
                <th>{{ $orderDiscount->observaciones }}</th>
            </tr>
            @endforeach
        </tbody>
    </table>
    <table>
        <thead>
            <tr>
                <th colspan="7" style="font-weight: bold">Platillos Cancelados</th>
            </tr>
            <tr>

                <th style="font-weight: bold">Día</th>
                <th style="font-weight: bold">Hora</th>
                <th style="font-weight: bold">H/C</th>
                <th style="font-weight: bold">Descripción</th>
                <th style="font-weight: bold">Precio</th>
                <th style="font-weight: bold">Autorizó</th>
                <th style="font-weight: bold">Orden</th>
                <th style="font-weight: bold">Observación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data->dishes_cancel as $orderDiscount)
            <tr>
                <th>{{$orderDiscount->dia}}</th>
                <th>{{$orderDiscount->hora}}</th>
                <th>{{$orderDiscount->hc}}</th>
                <th>{{ $orderDiscount->descripcion }}</th>
                <th>{{ str_replace("-", "", $orderDiscount->precio) }}</th>
                <th>{{ $orderDiscount->autorizo }}</th>
                <th>{{ $orderDiscount->clave }}</th>
                <th>{{ $orderDiscount->observaciones }}</th>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
