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
        //delay延迟5秒
        ProcessPodcast::dispatch(22222)->onConnection('database')->delay(5);
    }
    public function  test(){
        DB::table('test_job')->insert(['name'=>'队列测试']);
    }
    public function set(){
        session('id','111');
    }
}
