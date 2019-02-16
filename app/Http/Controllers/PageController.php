<?php

namespace App\Http\Controllers;

use App\Library\Drive;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;


/**
 * Page controller
 * Handle navigation for visitors
 */
class PageController extends Controller
{


    public function accueil()
    {
        if (url()->current() == url('/')) {
            return redirect()->route('accueil');
        }
        $info = self::getInfoPage("accueil");
        return view('accueil', ['pageTitle' => 'Accueil', 'texts' => $info['texts'], 'pictures' => $info['pictures'], 'page' => 'accueil']);
    }

    function getInfoPage($page)
    {

        $info = Cache::remember('info'.$page, 15, function() use ($page) {

            Log::info('Load cache info');

            $texts = [];
            $pictures = [];

            // TEXTS
            $datastore = initGoogleDatastore();

            $queryTexts = $datastore->query()
                ->kind('Text')
                ->filter('page', '=', $page);
            $resultTexts = $datastore->runQuery($queryTexts);
            foreach ($resultTexts as $resultText) {
                if (!array_key_exists($resultText->context, $texts)) {
                    $texts[$resultText->context] = array();
                }
                $texts[$resultText->context][$resultText->id] = htmlentities($resultText->value);
            }


            // IMG
            $queryPictures = $datastore->query()
                ->kind('Picture')
                ->filter('page', '=', $page)
                ->order('sort');
            $resultPictures = $datastore->runQuery($queryPictures);
            foreach ($resultPictures as $resultPicture) {
                if (!array_key_exists($resultPicture->context, $pictures)) {
                    $pictures[$resultPicture->context] = array();
                }
                array_push($pictures[$resultPicture->context], $resultPicture->get());
            }



            return array("texts" => $texts, "pictures" => $pictures);

        });

        return $info;




    }

    public function activites()
    {
        $info = self::getInfoPage("activites");
        return view('activites', ['pageTitle' => 'Activités', 'texts' => $info['texts'], 'pictures' => $info['pictures']]);
    }

    public function presentation()
    {
        $info = self::getInfoPage("presentation");
        return view('presentation', ['pageTitle' => 'Présentation', 'texts' => $info['texts'], 'pictures' => $info['pictures']]);
    }

    public function realisations()
    {
//        $time_start = microtime(true);

        $categories = Cache::remember('categories', 15, function() {
            Log::info('Load cache categories');
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


//        $time_end = microtime(true);
//        $time = $time_end - $time_start;
//        dd($time);

//        dd($categories);

        $info = self::getInfoPage("realisations");

        return view('realisations', ['pageTitle' => 'Réalisations', 'texts' => $info['texts'], 'pictures' => $info['pictures'], 'categories' => $categories]);
    }

    public function category($category_id)
    {

        $category = Cache::remember('category-'.$category_id, 15, function() use ($category_id) {
            $datastore = initGoogleDatastore();

            $queryCategories = $datastore->query()
                ->kind('Category')
                ->filter('url_friendly', '=', $category_id);
            $resultCategories = $datastore->runQuery($queryCategories);
            return $resultCategories->current()->get();
        });


        return view('category', ['category' => $category]);
    }

    public function presse()
    {
        $info = self::getInfoPage("presse");
        return view('presse', ['pageTitle' => 'Presse', 'texts' => $info['texts'], 'pictures' => $info['pictures']]);
    }

    public function contact()
    {
        $info = self::getInfoPage("contact");
        return view('contact', ['pageTitle' => 'Contact', 'texts' => $info['texts'], 'pictures' => $info['pictures']]);
    }

    public function mentions()
    {
        return view('mentions', ['pageTitle' => 'Mentions Légales']);
    }


    public function test()
    {
        // Sync sql texts and datastore texts

        $datastore = initGoogleDatastore();


        $query = $datastore->query()
            ->kind('Text');
        $result = $datastore->runQuery($query);

        $entitiesKey = [];

        foreach ($result as $entity) {
            $entitiesKey[] = $entity->key();
        }

        $datastore->deleteBatch($entitiesKey);

        $textAdd = [];

        $lines = DB::select('select page, context, id, value from texts');
        foreach ($lines as $row) {
            $key = $datastore->key('Text');
            $textAdd[] = $datastore->entity($key, [
                'page' => $row->page,
                'context' => $row->context,
                'id' => $row->id,
                'value' => $row->value
            ]);
        }

        $datastore->insertBatch($textAdd);






        $query = $datastore->query()
            ->kind('Picture');
        $result = $datastore->runQuery($query);

        $entitiesKey = [];

        foreach ($result as $entity) {
            $entitiesKey[] = $entity->key();
        }

        $datastore->deleteBatch($entitiesKey);

        $imgAdd = [];

        $lines = DB::select('select page, context, id, img_path, alt from pictures');
        foreach ($lines as $row) {
            $key = $datastore->key('Picture');
            $imgAdd[] = $datastore->entity($key, [
                'page' => $row->page,
                'context' => $row->context,
                'id' => $row->id,
                'alt' => $row->alt,
                'img_path' => $row->img_path
            ]);
        }

        $datastore->insertBatch($imgAdd);


        return "ok";
    }


}
