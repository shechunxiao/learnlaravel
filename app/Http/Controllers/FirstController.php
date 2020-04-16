<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FirstController extends Controller
{
    //
    public function index(){
        file_put_contents('./test.txt',"测试/r/n",FILE_APPEND);
    }
}
