<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controllers\CronController;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        
        //$schedule->call('App\Http\Controllers\CronController@createEnvio')->everyMinute();
        $schedule->call(function () {
            $cron =  new CronController;
            $cron->enviaEmail();
            $cron->cancelaCarrinhosSemUserID24Horas();
            $cron->geraPagamento();
            $cron->listarPagamentos();
            $cron->geraPagamentoServicoManutencao();
            $cron->updateAparelhos();
            $cron->enviaBoletoEmail();
            $cron->enviaBoletoEmail();
            $cron->verificaStatusPagamento();
            $cron->verificaPagamentoServicoManutencao();
        })->everyMinute();

        // $schedule->call(function () {
        //     $cron =  new CronController;
        // })->everyFiveMinutes();


    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
