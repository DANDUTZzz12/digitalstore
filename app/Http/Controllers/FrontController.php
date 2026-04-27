<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index()
    {
        // Nantinya kita akan tarik data dari database di sini.
        // Untuk sekarang, kita panggil tampilannya saja dulu.
        return view('welcome');
    }
}