<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function addFile()
    {

        return view('AddFile');
    }

    public function saveFile(Request $request)
    {
        $this->validate($request, [
            'title'  => 'required|max:50',
            "file"          => 'required',
            "tags"          => 'required',
        ]);
        $request_data = $request->all();
       // $response     = $products->add_new_product($request_data);
        return view('dashboard/AddProduct')->with("resp", $response);
    }
}
