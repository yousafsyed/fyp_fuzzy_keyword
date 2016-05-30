<?php

namespace App;

use Crypt;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class NgramModel extends Model
{
    protected $table = "ngrams";

    /**
     * [deleteNgramsByOriginalKey]
     * @param  [Array] $keys [Array of keys from database]
     * @return [Void]
     */
    public function deleteNgramsByOriginalKeys(Collection $keys)
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
    /**
     * [getNgramsByKey]
     * @param  String $key Encrypted Ngram key
     * @return Collection   $rowsMatched
     */
    public function getNgramsByKey($key)
    {

        $tag         = Crypt::decrypt($key);
        $rowsMatched = $this->all()->filter(function ($record) use ($tag) {
            if (Crypt::decrypt($record->ngram_key) == $tag) {
                $record->ngram_key    = Crypt::decrypt($record->ngram_key);
                $record->original_key = Crypt::decrypt($record->original_key);
                return $record;
            }
        });
        return $rowsMatched;

    }
}
