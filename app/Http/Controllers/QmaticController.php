<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QmaticController extends Controller
{
    /**
     * Afficher la page de présentation Qmatic
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('qmatic.index');
    }
}
