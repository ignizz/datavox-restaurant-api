<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ReportController;

class SendCashProofEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-mails';

    /**
     * The console Envio de correos programados por el administrador por horas
     *
     * @var string
     */
    protected $description = 'Envio de correos programados por el administrador por horas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reportControllerInstance = new ReportController;
        $result = $reportControllerInstance->sendCashProofEmail();
    }
}
