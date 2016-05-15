<?php
/**
 * Generate Encrypted Ngrams and Jaccard Coefficient of strings through this class
 */
namespace App\Libraries;

use Crypt;

class Ngram
{

    /**
     * Creats Encrypted Ngrams with OpenSSL (AES-256-CBC)
     * @param String
     * @return Array
     *
     */
    public function EncryptedNgrams($String)
    {
        $ngrams = array();
        $len    = strlen($String);
        for ($i = 0; $i < $len; $i++) {
            if ($i > ($n - 2)) {
                $ng = '';
                for ($j = $n - 1; $j >= 0; $j--) {
                    $ng .= $word[$i - $j];
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
        $union = array_unique(array_merge((array) $String1, (array) $str2));
        #Get Intersect of String1 and String2;
        $intersect = array_intersect((array) $String1, (array) $String2);

        $unionCount     = count($union);
        $intersectCount = count($intersect);

        return ($intersectCount / $unionCount);
    }
}
