<?php

namespace App\Http\Controllers;

use App\Library\Drive;
use Illuminate\Support\Facades\DB;


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

        $texts = array();
        $pictures = array();

        // TEXTS
        $lines = DB::select('select context, id, value from texts where page like ?', [$page]);
        foreach ($lines as $row) {
            // create context
            if (!array_key_exists($row->context, $texts)) {
                $texts[$row->context] = array();
            }
            // $texts[$row->context][$row->id] = nl2br(str_replace(" ", " &nbsp;", $row->value));
            $texts[$row->context][$row->id] = htmlentities($row->value);
        }

        // IMG
        $lines = DB::select('select context, id, img_path, alt from pictures where page like ? order by id', [$page]);
        foreach ($lines as $row) {
            // create context
            if (!array_key_exists($row->context, $pictures)) {
                $pictures[$row->context] = array();
            }
            $pictures[$row->context][$row->id] = array("img_path" => $row->img_path, "alt" => $row->alt);
        }


        return array("texts" => $texts, "pictures" => $pictures);
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
//        $time_end = microtime(true);
//        $time = $time_end - $time_start;
//        dd($time);

//        dd($categories);
        $info = self::getInfoPage("realisations");
        return view('realisations', ['pageTitle' => 'Réalisations', 'texts' => $info['texts'], 'pictures' => $info['pictures'], 'categories' => $categories]);
    }

    public function category($category_id)
    {

        $datastore = initGoogleDatastore();

        $queryCategories = $datastore->query()
            ->kind('Category')
            ->filter('url_friendly', '=', $category_id);
        $resultCategories = $datastore->runQuery($queryCategories);
        $category = $resultCategories->current()->get();




        //dd($albums_array);

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
        $datastore = initGoogleDatastore();



    }


}
