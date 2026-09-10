<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * Return string
     */
    public function test(): string
    {
        return "test";
    }
}
