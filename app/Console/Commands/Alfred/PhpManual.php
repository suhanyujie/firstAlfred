<?php

namespace App\Console\Commands\Alfred;

use App\Services\Alfred\Tool\SphpManual;
use Illuminate\Console\Command;

class PhpManual extends Command
{
    /**
     * The name and signature of the console command.
     * @cmd php artisan tool:phpManual addslashes
     * @var string
     */
    protected $signature = 'tool:phpManual {functionName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'php的函数手册';
    
    protected $phpManualEvent;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->phpManualEvent = new SphpManual();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // /usr/local/bin/php public/index.php php:functionManual {query}
        $string = $this->phpManualEvent->phpFunctionManual([
            'functionName' => $this->argument('functionName'),
        ]);
        echo $string;
    }
}
