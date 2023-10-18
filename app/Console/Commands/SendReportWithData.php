<?php

namespace App\Console\Commands;

use App\Models\Connection;
use Illuminate\Console\Command;
use App\Http\Controllers\ReportController;

class SendReportWithData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sendcustom {connection} {date}';

    /**
     * The console envia el correo pero con los parametros de la conexion y fecha de envio.
     *
     * @var string
     */
    protected $description = 'envia el correo pero con los parametros de la conexion y fecha de envio';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connection_id = $this->argument('connection');
        $connection = Connection::find($connection_id);
        if($connection == null){
            dd("no existe la conexión seleccionada");
        }
        $date = $this->argument('date');
        $reportControllerInstance = new ReportController;
        $result = $reportControllerInstance->sendCustomCashProofEmail($connection, $date);
    }
}
