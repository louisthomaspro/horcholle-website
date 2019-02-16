<?php

namespace App\Http\Composers;

use Illuminate\View\View;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class MenuComposer
{

    public function compose(View $view)
    {
        $categories_dd = Cache::rememberForever('categories', function() {
            $datastore = initGoogleDatastore();

            $queryCategories = $datastore->query()
                ->kind('Category')
//            ->projection(['name', 'thumbnail', 'url_friendly'])
                ->order('sort');
            $resultCategories = $datastore->runQuery($queryCategories);
            $categories = [];


            foreach ($resultCategories as $resultCategory) {
                $categories[] = $resultCategory->get();
            }

            return $categories;
        });

        $view->with('categories_dd', $categories_dd);
    }

}