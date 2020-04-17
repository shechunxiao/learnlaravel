<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPodcast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FirstController extends Controller
{
    //
    public function index(){
        file_put_contents('./test.txt',"测试/r/n",FILE_APPEND);
    }
    public function job(){
//        ProcessPodcast::dispatch(1)->onConnection('database');
        $this->dispatch(new ProcessPodcast(1));
    }
    public function  test(){
        DB::table('test_job')->insert(['name'=>'队列测试']);
    }
}
