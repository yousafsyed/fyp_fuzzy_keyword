<?php

namespace App\Http\Controllers;

use App\FilesModel;
use App\Libraries\FuzzySearch;
use Auth;
use Illuminate\Http\Request;
use Redirect;
use Crypt;

class HomeController extends Controller
{
    protected $FilesModel;
    protected $UserData;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(FilesModel $FilesModel)
    {
        $this->middleware('auth');
        $this->FilesModel = $FilesModel;
        $this->UserData   = Auth::user();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $files = $this->FilesModel->where('user_id', '=', $this->UserData->id)->paginate(5);
        return view('home')->with("files",$files);
    }

    public function addFile()
    {

        return view('AddFile');
    }

    public function saveFile(Request $request, FuzzySearch $FuzzySearch)
    {
        $this->validate($request, [
            'title' => 'required|max:50',
            "file"  => 'required',
            "tags"  => 'required',
        ]);
        $request_data = $request->all();
        $response     = $FuzzySearch->addNewFile($request_data);
        return Redirect::to('dashboard/addfile')->with('message', $response);
    }

    public function deteteFile(Request $request,FuzzySearch $FuzzySearch)
    {

        $request_data = $request->all();
        $response     = $FuzzySearch->deleteFile($request_data);
        return Redirect::to('home')->with('message', $response);
    }
}
