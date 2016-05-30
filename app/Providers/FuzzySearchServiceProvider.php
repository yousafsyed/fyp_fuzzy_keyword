<?php

namespace App\Providers;

use App\FileKeyInfoModel;
use App\FilesModel;
use App\Libraries\FuzzySearch;
use App\Libraries\Ngram;
use App\NgramModel;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Redirect;

class FuzzySearchServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Libraries\FuzzySearch', function () {

            $FilesModel       = $this->app->make(FilesModel::class);
            $Ngram            = $this->app->make(Ngram::class);
            $FileKeyInfoModel = $this->app->make(FileKeyInfoModel::class);
            $NgramModel       = $this->app->make(NgramModel::class);
            $Request          = $this->app->make(Request::class)->all();

            $fileId = (isset($Request['file_id'])) ? $Request['file_id'] : null;
            try {
                return new FuzzySearch($FilesModel, $Ngram, $FileKeyInfoModel, $NgramModel, $fileId);
            } catch (\Exception $e) {
                redirect('home')->send()->with('message', 'Error:' . $e->getMessage());

            }

        });
    }
}
