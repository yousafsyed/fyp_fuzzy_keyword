<?php
/**
 * @author  Yousaf Syed
 * @copyright 2016
 */
namespace App\Libraries;

use App\FileKeyInfoModel;
use App\FilesModel;
use App\Libraries\FuzzyObject;
use App\Libraries\Ngram;
use App\NgramModel;
use Auth;
use Crypt;
use Exception;
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
    private $NgramModel;
    private $fileInfo = null;
    /**
     *
     * @param FilesModel $FilesModel
     * @param Ngram      $Ngram
     */
    public function __construct(FilesModel $FilesModel, Ngram $Ngram, FileKeyInfoModel $FileKeyInfoModel, NgramModel $NgramModel, $fileId)
    {
        $this->user_data        = Auth::user();
        $this->user_id          = $this->user_data->id;
        $this->file_dir_path    = "UserFiles" . DIRECTORY_SEPARATOR . $this->user_id . "_Files";
        $this->FilesModel       = $FilesModel;
        $this->Ngram            = $Ngram;
        $this->FileKeyInfoModel = $FileKeyInfoModel;
        $this->NgramModel       = $NgramModel;
        if (!$this->checkFileAuth($fileId)) {
            throw new Exception('Unauthorized action.');
        }
    }

    /**
     * [checkFileAuth Check the file if it belongs to current user other than that return false]
     * @param  Integer $fileId file id from request
     * @return Boolean
     */
    public function checkFileAuth($fileId)
    {
        if (is_numeric($fileId)) {
            $this->fileInfo = $this->FilesModel->where('id', $fileId)->first();

            if ($this->fileInfo == null || $this->fileInfo->user_id != $this->user_data->id) {
                return false;
            } else {
                return true;
            }

        }
        return true;
    }
    /**
     * [getFile]
     * @return Array       Array of data
     */
    public function getFile()
    {
        if ($this->fileInfo != null) {
            return [
                'path' => $this->file_dir_path . DIRECTORY_SEPARATOR . $this->fileInfo->filename,
                'name' => $this->fileInfo->filename,
            ];
        } else {
            throw new Exception('You are not authorized to download this file');
        }
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
            return "Success: File added Successfully";
        } else {
            throw new Exception("Something went wrong please contact support.");
        }
    }
    /**
     * [listFiles]
     * @param  Array $data Request data array
     * @return [type]
     */
    public function listFiles($data)
    {
        if (isset($data['q'])) {
            $keys                    = explode(' ', $data['q']);
            $countOfKeys             = count($keys);
            $FuzzySet                = $this->FuzzySet($keys);
            $JaccardCoefficientArray = $this->JaccardCoefficientForFuzzySet($FuzzySet);
            $FuzzySetArray           = array();
            $OriginalSetArray        = array();
            $this->DivideFuzzyAndOriginalSets($FuzzySetArray, $OriginalSetArray, $FuzzySet);
            $CorrectedKeys = $this->CorrectKeysByJaccardArray($keys, $JaccardCoefficientArray, $FuzzySetArray, $OriginalSetArray);
            return $this->FileKeyInfoModel->FileIdsByKeys($CorrectedKeys);

        }
    }

    public function DivideFuzzyAndOriginalSets(&$FuzzySetArray, &$OriginalSetArray, $FuzzySet)
    {

        foreach ($FuzzySet as $row) {
            $FuzzySetArray[]    = $row->fuzzy_key;
            $OriginalSetArray[] = $row->original_key;

        }
    }
    public function CorrectKeysByJaccardArray($keys, &$JaccardCoefficientArray, &$FuzzySetArray, &$OriginalSetArray)
    {
        $correctedKeys = array();
        $len           = count($JaccardCoefficientArray);
        for ($i = 0; $i < count($keys); $i++) {
            $max = -1;
            for ($j = 0; $j < $len; $j++) {
                if ($keys[$i] === $OriginalSetArray[$j]) {
                    if ($max < $JaccardCoefficientArray[$j]) {
                        $max               = $JaccardCoefficientArray[$j];
                        $correctedKeys[$i] = $FuzzySetArray[$j];
                    }
                }

            }
            $max = -1;
        }
        return array_unique($correctedKeys, SORT_REGULAR);
    }
    public function FuzzySet($keys)
    {
        $FuzzySetArray = array();

        foreach ($keys as $key) {
            $ngrams = $this->Ngram->EncryptedNgrams($key);
            foreach ($ngrams as $key2) {

                $tmpFuzzySet = $this->NgramModel->getNgramsByKey($key2);
                if ($tmpFuzzySet == null) {continue;}
                $this->createFuzzyObject($tmpFuzzySet, $FuzzySetArray, $key);
            }

        }
        return array_unique($FuzzySetArray, SORT_REGULAR);

    }

    public function createFuzzyObject($tmpFuzzySet, &$FuzzySetArray, $key)
    {
        foreach ($tmpFuzzySet as $key3) {
            $FuzzyObject               = new FuzzyObject();
            $FuzzyObject->original_key = $key;
            $FuzzyObject->fuzzy_key    = $key3->original_key;
            $FuzzySetArray[]           = $FuzzyObject;
        }
    }

    public function JaccardCoefficientForFuzzySet($FuzzySet)
    {
        $JaccardCoefficientArray = array();
        foreach ($FuzzySet as $row) {
            $JaccardCoefficientArray[] = $this->Ngram->JaccardCoefficient($row->fuzzy_key, $row->original_key);
        }
        return $JaccardCoefficientArray;
    }

    /**
     * [deleteFile]
     * @param  String $data
     * @return String
     */
    public function deleteFile()
    {

        if ($this->fileInfo != null) {

            $this->deleteFileFromStorage($this->fileInfo);
            //delete FileKeys and return collections of deleted keys
            $FileKeysCollection = $this->FileKeyInfoModel->deleteFileKeys($this->fileInfo);

            //delete Ngrams based on Keys
            $this->NgramModel->deleteNgramsByOriginalKeys($FileKeysCollection);

            return "Success: File Deleted Successfully";
        } else {
            throw new Exception("File already deleted or donot exist");
        }

    }
    /**
     * [deleteFileFromStorage]
     * @param  FileModel $fileInfo
     * @return Void
     */
    public function deleteFileFromStorage(FilesModel $fileInfo)
    {
        $file_storage_path = $this->file_dir_path . DIRECTORY_SEPARATOR . $fileInfo->filename;
        if (Storage::disk('local')->has($file_storage_path)) {
            Storage::disk('local')->delete($file_storage_path);
        }
        //delete file from table
        $fileInfo->delete();
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
     * @param  String $string
     * @return String
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
     * @param  String $string
     * @return String
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
     * @param  File $file
     * @return String
     */
    public function file_name_and_extension($file)
    {
        $file_extension = $file->getClientOriginalExtension();
        $file_unique_id = uniqid();
        $file_name      = $file->getClientOriginalName();
        $file_name      = pathinfo($file_name, PATHINFO_FILENAME);
        if (empty($file_extension)) {
            $file_name = $file_name . "_" . $file_unique_id;
        } else {
            $file_name = $file_name . "_" . $file_unique_id . '.' . $file_extension;
        }

        return $file_name;
    }
}
