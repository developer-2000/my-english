<?php

namespace App\Http\Controllers;

class SpaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:user']);
    }

    public function index()
    {
        // Очищаем любой возможный output buffer
        if (ob_get_level()) {
            ob_clean();
        }

        return view('index');
    }
}
