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
    $headers = [
        "empleado" => "Nombre Empleado",
        "sabado" => "Sabado",
        "domingo" => "Domingo",
        "lunes" => "Lunes",
        "martes" => "Martes",
        "miercoles" => "Miercoles",
        "jueves" => "Jueves",
        "viernes" => "Viernes",
        "total_hours" => "Total Horas",
    ];
    @endphp
    <table>
        <thead>
            <tr>
                @foreach($data[0] as $index => $v)
                <th style="font-weight: bold">{{$headers[$index]}}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                @foreach($row as $value)
                <th>{{ $value }}</th>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
