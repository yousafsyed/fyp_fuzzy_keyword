<?php
/**
 *
 */
namespace App\Libraries;

use Auth;
use File;
use Storage;
use Crypt;

class FuzzySearch
{
    private $user_data;
    private $user_id;
    private $file_dir_path;

    public function __construct()
    {
        $this->user_data        = Auth::user();
        $this->user_id          = $this->user_data->user_id;
        $this->file_dir_path    = "UserFiles" . DIRECTORY_SEPARATOR . $this->user_id . "_Files";
    }

    public function addNewFile($data)
    {
        $title      = $data['title'];

        $file_name         = $this->file_name_and_extension($data['file']);
        $file_storage_path = $this->file_dir_path . DIRECTORY_SEPARATOR  . $file_name;
        $file_extension    = $data['file']->getClientOriginalExtension();
        Storage::disk('local')->put($file_storage_path, File::get($data['file']));

        $picture_files = $data['picture_files'];
        foreach ($picture_files as $picture) {
            $pic_file_name           = $this->file_name_and_extension($picture);
            $pic_storage_path        = $this->picture_dir_path . DIRECTORY_SEPARATOR . $product_dir_name . DIRECTORY_SEPARATOR . $pic_file_name;
            $all_pics_storage_path[] = $pic_storage_path;
            Storage::disk('local')->put($pic_storage_path, File::get($picture));

        }
        $this->productsModel->user_id = $this->user_id;

        $this->productsModel->title                 = $this->trim($product_name);
        $this->productsModel->product_storage_path  = $file_storage_path;
        $this->productsModel->product_image_path    = json_encode($all_pics_storage_path);
        $this->productsModel->product_price         = trim($data['product_price']);
        $this->productsModel->product_currency_code = trim($data['currency']);
        $this->productsModel->product_tags          = trim($data['tags']);
        $this->productsModel->product_description   = trim($data['description']);
        $this->productsModel->product_extension     = $file_extension;
        if ($this->productsModel->save()) {
            return array('success' => "Product Created Successfully");
        } else {
            return array('error' => "Some error Occured");
        }
    }
}
