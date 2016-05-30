<?php
/**
 * Generate Encrypted Ngrams and Jaccard Coefficient of strings through this class
 * @author  Yousaf Syed
 * @copyright 2016
 *
 */
namespace App\Libraries;

use App\NgramModel;
use Crypt;

class Ngram
{
    protected $NgramModel;
    /**
     * [__construct]
     * @param NgramModel $NgramModels
     */
    public function __construct(NgramModel $NgramModel)
    {
        $this->NgramModel = $NgramModel;
    }

    /**
     * Creats Encrypted Ngrams with OpenSSL (AES-256-CBC)
     * @param  String $String [String or word]
     * @return Array $encNgrams [Encrypted array for Ngrams]
     *
     */
    public function EncryptedNgrams($String, $n = 2)
    {
        $ngrams = array();
        $len    = strlen($String);
        for ($i = 0; $i < $len; $i++) {
            if ($i > ($n - 2)) {
                $ng = '';
                for ($j = $n - 1; $j >= 0; $j--) {
                    $ng .= $String[$i - $j];
                }
                $ngrams[] = $ng;
            }
        }

        $encNgrams = array();
        $i         = 0;
        foreach ($ngrams as $row) {

            $encNgrams[$i++] = Crypt::encrypt($row);
        }
        return $encNgrams;
    }
    /**
     *
     * @param Array $enc_ngrams
     * @param String $key
     */
    public function AddNgramsToDB($enc_ngrams, $key)
    {
        $data = array();
        foreach ($enc_ngrams as $ngram) {
            array_push($data, array("ngram_key" => $ngram, "original_key" => $key));
        }

        if (count($data)) {
            $this->NgramModel->insert($data);
            return true;
        } else {
            return false;
        }

    }

    /**
     * Jacard Coefficient of two strings
     * @param  String
     * @param  String
     * @return Integer
     */
    public function JaccardCoefficient($String1, $String2)
    {

        #Split Strings into arrays by each digit;
        $String1 = array_unique(str_split($String1));
        $String2 = array_unique(str_split($String2));

        #Get Union of  String1 and String2
        $union = array_unique(array_merge((array) $String1, (array) $String2));
        #Get Intersect of String1 and String2;
        $intersect = array_intersect((array) $String1, (array) $String2);

        $unionCount     = count($union);
        $intersectCount = count($intersect);

        return ($intersectCount / $unionCount);
    }
}
