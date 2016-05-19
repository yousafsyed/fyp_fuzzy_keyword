<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Libraries\FuzzySearch;

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

    public function saveFile(Request $request,FuzzySearch $FuzzySearch)
    {
        $this->validate($request, [
            'title'  => 'required|max:50',
            "file"          => 'required',
            "tags"          => 'required',
        ]);
        $request_data = $request->all();
        $response     = $FuzzySearch->addNewFile($request_data);
        return redirect('dashboard/addfile')->with("resp", $response);
    }
}
