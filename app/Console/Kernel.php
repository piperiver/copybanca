<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Librerias\UtilidadesClass;


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
        
        $schedule->call(function () {            
            /*
             * Proceso para realizar la causacion diaria
             */
            \Log::debug('Tarea programada CAUSACION ejecutada satisfactoriamente');
            $objCartera = new \App\Http\Controllers\CarteraController();
            $objCartera->cronJobsCausacion();  
            
            
            /*
             * Proceso para vencer las certificaciones viejas
             */
            $certificaciones = LogCertificaciones::where("estado", "1")->get();
        
            foreach($certificaciones as $certificacion){
                $fechaCorte = strtotime($certificacion->diaCorte."-".$certificacion->mesVigencia." ".$certificacion->anioVigencia);
                if(strtotime("now") > $fechaCorte){
                    $updateCertificacion = LogCertificaciones::where("id", $certificacion->id)->update(["estado" => "0"]);
                }
            }
            
        })->daily();
        
        
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
