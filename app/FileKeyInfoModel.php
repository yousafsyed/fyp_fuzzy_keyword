<?php

namespace App;

use Crypt;
use Illuminate\Database\Eloquent\Model;

class FileKeyInfoModel extends Model
{
    protected $table = "FileKeyInfo";

    public function addKeys($keys, $fileId)
    {
        $fileKeyInfo = array();
        foreach ($keys as $key) {
            array_push($fileKeyInfo, ["file_id" => $fileId, "key" => Crypt::encrypt(strtolower($key))]);
        }
        return $this->insert($fileKeyInfo);
    }

    /**
     * [deleteFileKeys]
     * @param  FilesModel $fileInfo
     * @return Collection            File Key Collection
     */
    public function deleteFileKeys(FilesModel $fileInfo)
    {
        return $this->where('file_id', $fileInfo->id)->get()->each(function ($fileKey) {
            $fileKey->delete();
        });
    }

    public function FileIdsByKeys($keys)
    {   
        $file_ids= array();
        foreach ($keys as $tag) {
            $f_ids = $this->all()->filter(function ($record) use ($tag) {
                if (Crypt::decrypt($record->key) == $tag) {
                   
                    return $record->file_id;
                }
            });
            foreach ($f_ids as $f_id) {
                $file_ids[] = $f_id->file_id;
            }
            
        }

        return $file_ids;

    }

}
