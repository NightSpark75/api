<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\Testlog::class,
        \App\Console\Commands\QAOverdue::class,
        \App\Console\Commands\MPZOverdue0830::class,
        \App\Console\Commands\MPZOverdue1330::class,
        \App\Console\Commands\MPZOverdue1700::class,
        \App\Console\Commands\MPZOverdue2200::class,
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
        // 每分鐘執行 Artisan 命令 test:Log
        $schedule->command('test:Log')->everyMinute();
        $schedule->command('Web:QA:Overdue')->dailyAt('07:00');
        //$schedule->command('Web:MPZ:expired')->dailyAt('07:00');
        $schedule->command('Web:MPZ:Overdue0830')->dailyAt('08:30');
        $schedule->command('Web:MPZ:Overdue0830')->dailyAt('09:55');
        $schedule->command('Web:MPZ:Overdue1330')->dailyAt('13:30');
        $schedule->command('Web:MPZ:Overdue1700')->dailyAt('17:00');
        $schedule->command('Web:MPZ:Overdue2200')->dailyAt('22:00');
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
