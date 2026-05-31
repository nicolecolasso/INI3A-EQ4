<?php

namespace App\Http\Controllers;

class InstitucionalController
{
    public function index()
    {
        return view('institucional.index');
    }

    public function quemSomos()
    {
        return view('institucional.quemSomos');
    }
}
