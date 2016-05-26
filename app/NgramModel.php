<?php

namespace App;

use Crypt;
use Illuminate\Database\Eloquent\Model;

class NgramModel extends Model
{
    protected $table = "ngrams";

    /**
     * [deleteNgramsByOriginalKey]
     * @param  [Array] $keys [Array of keys from database]
     * @return [Void]      
     */
    public function deleteNgramsByOriginalKeys($keys)
    {

        foreach ($keys as $key) {
            $tag          = Crypt::decrypt($key->key);
            $rowsToDelete = $this->all()->filter(function ($record) use ($tag) {
                if (Crypt::decrypt($record->original_key) == $tag) {
                    return $record;
                }
            });

            foreach ($rowsToDelete as $row) {
                $this->where('id', $row->id)->delete();
            }

        }
    }
}
