<?php

namespace App\Http\Controllers;

use App\FilesModel;
use App\Libraries\FuzzySearch;
use Auth;
use Crypt;
use Illuminate\Http\Request;
use Redirect;
use Response;
use Storage;

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
    public function index(Request $request, FuzzySearch $FuzzySearch)
    {
        $data = $request->all();

        if (isset($data['q'])) {
            $fileIds = $FuzzySearch->listFiles($data);
            $files   = $this->FilesModel->whereIn('id', $fileIds)->where('user_id', '=', $this->UserData->id)->paginate(5);
        } else {
            $files = $this->FilesModel->where('user_id', '=', $this->UserData->id)->paginate(5);
        }

        return view('home')->with("files", $files);
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
        try {
            $response = $FuzzySearch->addNewFile($request_data);
        } catch (\Exception $e) {
            $response = "Error:" . $e->getMessage();
        }
        return Redirect::to('dashboard/addfile')->with('message', $response);
    }

    public function DownloadFile(Request $request, FuzzySearch $FuzzySearch)
    {

        try {
            $fileInfo = $FuzzySearch->getFile();

            $file = Crypt::decrypt(Storage::get($fileInfo['path']));
            $type = Storage::mimeType($fileInfo['path']);

            $response = Response::make($file, 200);
            $response->header("Content-Type", $type);
            $response->header('Content-Disposition', 'attachment; filename="' . $fileInfo['name'] . '"');

            return $response;
        } catch (\Exception $e) {
            return Redirect::to('home')->with('message', 'Error:' . $e->getMessage());
        }

    }

    public function deteteFile(Request $request, FuzzySearch $FuzzySearch)
    {

        $request_data = $request->all();

        try {
            $response = $FuzzySearch->deleteFile($request_data);
        } catch (\Exception $e) {
            $response = "Error:" . $e->getMessage();
        }

        return Redirect::to('home')->with('message', $response);
    }
}
