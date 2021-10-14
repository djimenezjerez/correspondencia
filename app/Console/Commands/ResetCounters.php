<?php

namespace App\Console\Commands;

use App\Models\ProcedureType;
use App\Models\Area;
use Illuminate\Console\Command;

class ResetCounters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:counters';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reinicio de contadores de hojas de ruta';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ProcedureType::where('counter', '>', 0)->update([
            'counter' => 0,
        ]);
        Area::where('counter', '>', 0)->update([
            'counter' => 0,
        ]);
    }
}
