<?php

namespace App\Console\Commands\Alfred;

use Illuminate\Console\Command;
use App\Services\Alfred\Tool\Syoudao;

class Youdao extends Command
{
    /**
     * The name and signature of the console command.
     * @cmd php artisan transfer:youdao {query}
     * @var string
     */
    protected $signature = 'transfer:youdao {query}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'alfred的功能：有道翻译词典';

    protected $youdaoEvent;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->youdaoEvent = new Syoudao();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $string = $this->youdaoEvent->youdao([
            'keyword' => $this->argument('query'),
        ]);
        echo $string;
    }
}
