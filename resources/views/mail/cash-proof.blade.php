<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corte general</title>
</head>
<body style="font-family: Arial, sans-serif;">
    <div class="row wrap justify-center items-start content-center">
        <div style="display: block;max-width: 365px;margin:20px auto;border: solid 1px rgba(0, 0, 0, 0.481);padding: 15px;" class="q-card q-card--bordered q-card--flat no-shadow col-lg-3 col-sm-6 col-xs-12 text-center">
            <div class="q-card__section q-card__section--vert"><div style="font-size: 1.25rem;
                font-weight: 500;
                line-height: 2rem;
                letter-spacing: .0125em;" class="text-h6">Corte General</div>
            @foreach ($data->header as $header)
                <div style="font-size: 1rem;
        font-weight: 400;
        line-height: 1.5rem;
        letter-spacing: .03125em;
        overflow:hidden;
        white-space: nowrap;text-align: justify;
        @if(!$header->title=='Fecha') text-align:center; @endif
        -webkit-hyphens: auto;
        hyphens: auto;" class="text-body1  @if($header->title=='Fecha') text-justify @endif"> {{ $header->title." ".$header->value  }}  </div>
            @endforeach
            <div style="font-size: 1rem;
        font-weight: 400;
        line-height: 1.5rem;
        letter-spacing: .03125em;
        overflow:hidden;
        white-space: nowrap;text-align: justify;
        -webkit-hyphens: auto;
        hyphens: auto;" class="text-body1 text-justify linear-data">------------------------------------------------------------</div>
            <div style="font-size: 1rem;
        font-weight: 400;
        line-height: 1.5rem;
        letter-spacing: .03125em;
        overflow:hidden;
        white-space: nowrap;" class="text-body1 text-center">DESGLOSE POR SERVICIO</div>
            <div style="font-size: 1rem;
        font-weight: 400;
        line-height: 1.5rem;
        letter-spacing: .03125em;
        overflow:hidden;
        white-space: nowrap;text-align: justify;
        -webkit-hyphens: auto;
        hyphens: auto;" class="text-body1 text-justify linear-data">------------------------------------------------------------</div>
            @foreach ($data->services as $service)
            @php
                $limit = 34;
                $htmlTab = "";
                $tab = trim($service->title);
                if($tab == "Cobrado Caja" || $tab== "Cobrado Reparto" || $tab=="Cobrado"){
                    $htmlTab = "&emsp;";
                    $limit = 30;
                }
            @endphp
            <div style="font-size: 1rem;
        font-weight: 400;
        line-height: 1.5rem;
        letter-spacing: .03125em;
        overflow:hidden;
        white-space: nowrap;text-align: justify;
        -webkit-hyphens: auto;
        hyphens: auto;" class="text-body1 text-justify">
                <p style="overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
                display: inline-grid;
                margin: 0;
                padding: 0;
                display: inline-grid;
                width: 60%;" class="linear-ellipsis inline">{!! $htmlTab !!} {{ Str::limit($service->title."....................................", $limit, "") }}</p>
                <p style="overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
                display: inline-grid;
                margin: 0;
                padding: 0;
                display: inline-grid;
                width: 40%;" class="linear-data inline-2"> :{{ $service->value }}</p>
            </div>
            @endforeach

            <div style="font-size: 1rem;
        font-weight: 400;
        line-height: 1.5rem;
        letter-spacing: .03125em;
        overflow:hidden;
        white-space: nowrap;text-align: justify;
        -webkit-hyphens: auto;
        hyphens: auto;" class="text-body1 text-justify linear-data">------------------------------------------------------------</div>
            @foreach ($data->totals as $totals)
            <div style="font-size: 1rem;
        font-weight: 400;
        line-height: 1.5rem;
        letter-spacing: .03125em;
        overflow:hidden;
        white-space: nowrap;text-align: justify;
        -webkit-hyphens: auto;
        hyphens: auto;" class="text-body1 text-justify">
                <p style="overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
                display: inline-grid;
                margin: 0;
                padding: 0;
                width: 60%;" class="linear-ellipsis inline">{{ Str::limit($totals->title."....................................", 34, "") }}</p>

                <p style="overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
                display: inline-grid;
                margin: 0;
                padding: 0;
                width: 40%;" class="linear-data inline-2"> :{{ $totals->value }}</p>
            </div>
            @endforeach
            <div style="font-size: 1rem;
        font-weight: 400;
        line-height: 1.5rem;
        letter-spacing: .03125em;
        overflow:hidden;
        white-space: nowrap;text-align: justify;
        -webkit-hyphens: auto;
        hyphens: auto;" class="text-body1 text-justify linear-data">------------------------------------------------------------</div>
            <div style="font-size: 1rem;
        font-weight: 400;
        line-height: 1.5rem;
        letter-spacing: .03125em;
        overflow:hidden;
        white-space: nowrap;text-align:center;" class="text-body1">PAGOS REALIZADOS CON:</div>
            <div style="font-size: 1rem;
        font-weight: 400;
        line-height: 1.5rem;
        letter-spacing: .03125em;
        overflow:hidden;
        white-space: nowrap;text-align: justify;
        -webkit-hyphens: auto;
        hyphens: auto;" class="text-body1 text-justify linear-data">------------------------------------------------------------</div>
            @foreach ($data->total_payed as $totalPayed)
            <div style="font-size: 1rem;
        font-weight: 400;
        line-height: 1.5rem;
        letter-spacing: .03125em;
        overflow:hidden;
        white-space: nowrap;text-align: justify;
        -webkit-hyphens: auto;
        hyphens: auto;" class="text-body1 text-justify">
                <p style="overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
                display: inline-grid;
                margin: 0;
                padding: 0;
                display: inline-grid;
                width: 60%;" class="linear-ellipsis inline">{{ Str::limit($totalPayed->title."....................................", 34, "") }}</p>
                <p style="overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
                display: inline-grid;
                margin: 0;
                padding: 0;
                display: inline-grid;
                width: 40%;" class="linear-data inline-2"> :{{ $totalPayed->value }}</p>
            </div>
            @endforeach
        </div>
    </div>
</body>
</html>
