<?php
/**
 *
 */
namespace App\Libraries;

use App\FileKeyInfoModel;
use App\FilesModel;
use App\Libraries\Ngram;
use Auth;
use Crypt;
use File;
use Storage;

class FuzzySearch
{
    private $user_data;
    private $user_id;
    private $file_dir_path;
    private $FilesModel;
    private $Ngram;
    private $FileKeyInfoModel;
    /**
     *
     * @param FilesModel $FilesModel
     * @param Ngram      $Ngram
     */
    public function __construct(FilesModel $FilesModel, Ngram $Ngram, FileKeyInfoModel $FileKeyInfoModel)
    {
        $this->user_data        = Auth::user();
        $this->user_id          = $this->user_data->id;
        $this->file_dir_path    = "UserFiles" . DIRECTORY_SEPARATOR . $this->user_id . "_Files";
        $this->FilesModel       = $FilesModel;
        $this->Ngram            = $Ngram;
        $this->FileKeyInfoModel = $FileKeyInfoModel;
    }
    /**
     * [addNewFile]
     * @param [array] $data [description]
     */
    public function addNewFile($data)
    {

        $this->FilesModel->user_id  = $this->user_id;
        $this->FilesModel->title    = $this->trim($data['title']);
        $this->FilesModel->filename = $this->handleAndStoreFile($data['file']);
        $tags                       = $this->handleKeysAndEngrams($data['tags']);
        if ($this->FilesModel->save()) {
            $this->FileKeyInfoModel->addKeys($tags, $this->FilesModel->id);
            return array('success' => "File added Successfully");
        } else {
            return array('error' => "Some error Occured");
        }
    }

    /**
     * [handleKeysAndEngrams ]
     * @param  String $tags
     * @return Array $tags
     */
    public function handleKeysAndEngrams($tags)
    {
        $tags = explode(' ', $tags);
        foreach ($tags as $key) {
            $key        = strtolower($key);
            $enc_ngrams = $this->Ngram->EncryptedNgrams($key);
            $key        = Crypt::encrypt($key);
            $this->Ngram->AddNgramsToDB($enc_ngrams, $key);
        }

        return $tags;
    }
    /**
     * [handleAndStoreFile]
     * @param  String $value
     * @return String $file_name
     */
    public function handleAndStoreFile($file)
    {
        $file_name         = $this->file_name_and_extension($file);
        $file_storage_path = $this->file_dir_path . DIRECTORY_SEPARATOR . $file_name;
        $file_extension    = $file->getClientOriginalExtension();
        //store and encrypt file
        Storage::disk('local')->put($file_storage_path, Crypt::encrypt(File::get($file)));
        return $file_name;
    }
    /**
     * return the string with spaces and trimed for all special caracters
     * @param  [string] $string
     * @return [string]
     */
    public function trim($string)
    {
        $string = str_replace(' ', '-', $string);
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
        $string = str_replace('-', ' ', $string);
        return trim($string);
    }
    /**
     * [product_dir_name description]
     * @param  [String] $string
     * @return [String]
     */
    public function product_dir_name($string)
    {
        $string = ucwords(strtolower($string));
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special char
        $string = $string . '-' . uniqid();
        return $string;
    }
    /**
     * [file_name_and_extension description]
     * @param  [file] $file
     * @return [string]
     */
    public function file_name_and_extension($file)
    {
        $file_extension = $file->getClientOriginalExtension();
        $file_unique_id = uniqid();
        $file_name      = $file->getClientOriginalName();
        $file_name      = pathinfo($file_name, PATHINFO_FILENAME);
        if (empty($file_extension)) {
            $file_name = $file_name . "_" . $file_unique_id ;
        } else {
            $file_name = $file_name . "_" . $file_unique_id . '.' . $file_extension;
        }

        return $file_name;
    }
}
