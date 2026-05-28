<?php

namespace App\Http\Controllers\Igd;

use App\Http\Controllers\Controller;
use Request;

class PemeriksaanIgdController extends Controller
{
    public function __construct()
    {
        $this->middleware('loginauth');
        $this->middleware('decrypt');
    }

    public function index()
    {
        return view('igd.pemeriksaan-igd');
    }
}
