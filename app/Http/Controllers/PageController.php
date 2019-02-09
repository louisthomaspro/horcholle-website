<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;


use App\Library\Drive;


/**
* Page controller
* Handle navigation for visitors
*/
class PageController extends Controller
{



  function getInfoPage($page) {
    
    $texts = array();
    $pictures = array();

    // TEXTS
    $lines = DB::select('select context, id, value from texts where page like ?', [ $page ]);
    foreach ($lines as $row) {
      // create context
      if (!array_key_exists($row->context, $texts)) {
        $texts[$row->context] = array();
      }
      // $texts[$row->context][$row->id] = nl2br(str_replace(" ", " &nbsp;", $row->value));
      $texts[$row->context][$row->id] = htmlentities($row->value);
    }

    // IMG
    $lines = DB::select('select context, id, img_path, alt from pictures where page like ? order by id', [ $page ]);
    foreach ($lines as $row) {
      // create context
      if (!array_key_exists($row->context, $pictures)) {
        $pictures[$row->context] = array();
      }
      $pictures[$row->context][$row->id] = array("img_path" => $row->img_path, "alt" => $row->alt);
    }


    return array("texts" => $texts, "pictures" => $pictures);
  }


  public function accueil()
  {
    if (url()->current() == url('/')) {
      return redirect()->route('accueil');
    }
    $info = self::getInfoPage("accueil");
    return view('accueil', ['pageTitle' => 'Accueil', 'texts' => $info['texts'], 'pictures' => $info['pictures'], 'page' => 'accueil']);
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
    
    // $categories = DB::select('
    //   select c.name, c.thumbnail_id, c.id, p.url from category c
    //   INNER JOIN picture p on c.thumbnail_id = p.id
    // ');

    $categories = DB::select('
      select category.id, category.filename, image.img_path from realisations category
      left join realisations image on category.path = image.dirname
      where category.hierarchy = 1 and image.mimetype in ("image/jpeg", "image/png", "image/gif", "image/bmp")
    ');
    $info = self::getInfoPage("realisations");
    return view('realisations', ['pageTitle' => 'Réalisations', 'texts' => $info['texts'], 'pictures' => $info['pictures'], 'categories' => $categories]);
  }

  public function category($category_id)
  {

    $category_name = DB::select('select name from realisations where id=?',[$category_id])[0]->name;

    // changer "image/%" en integer
    $albums = DB::select('
      select album.id, album.name, description.comment, image.filename, image.img_path from realisations album
      inner join realisations image on image.parent_id = album.id
      left join realisations description on description.dirname = album.path and description.mimetype like "application/vnd.google-apps.document"
      where album.parent_id=? and image.mimetype in ("image/jpeg", "image/png", "image/gif", "image/bmp")
      ',[$category_id]);

    $albums_array = array();
    foreach ($albums as $album) {
      // create album
      if (!array_key_exists($album->id, $albums_array)) {
        $albums_array[$album->id] = array("name" => $album->name, "description" => nl2br($album->comment), "pictures" => array());
      }
      // add pictures to album
      array_push($albums_array[$album->id]["pictures"], array("name" => $album->filename, "url" => $album->img_path));
    }

    //dd($albums_array);

    return view('category', ['category_name' => $category_name, 'albums' => $albums_array]);
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

  }




}
