<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SecondConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SecondConsole';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return mixed
     */
    public function handle()
    {
        //
        file_put_contents('./made.txt','测试\\r\\n',FILE_APPEND);
        echo 'test'.'<br/>';
    }
}
