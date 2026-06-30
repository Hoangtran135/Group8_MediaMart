<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function about()
    {
        return view('frontend.about');
    }

    public function policy()
    {
        return view('frontend.policy');
    }
}
