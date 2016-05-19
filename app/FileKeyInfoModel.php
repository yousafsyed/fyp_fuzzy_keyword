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
            array_push($fileKeyInfo, ["file_id" => $fileId, "key" => Crypt::encrypt($key)]);
        }
        return $this->insert($fileKeyInfo);
    }
}
