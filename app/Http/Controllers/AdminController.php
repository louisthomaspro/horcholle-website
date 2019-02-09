<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


use Storage;
use Imagick;
use Config;

/**
 * Admin panel controller
 * Handle navigation for the administrator
 */
class AdminController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    // https://github.com/ivanvermeyen/laravel-google-drive-demo

    // Return array of $b items not in $a
  function flip_isset_diff($b, $a) { // very little bit faster
    $at = array_flip($a);
    $d = array();
    foreach ($b as $i)
      if (!isset($at[$i]))
        $d[] = $i;
    return $d;
  }

  // Map each $item in $array with $item[$key] key value
  function map_with($array, $key) {
    $array_with_keys = array_fill_keys(self::extract_key($array, $key), 'test');
    for ($i=0; $i < count($array); $i++) { 
      $key_value = $array[$i][$key];
      $array_with_keys[$key_value] = $array[$i];
    }
    return $array_with_keys;
  }

  // Extract all $item[$key] values from $array
  function extract_key($array, $key) {
    $new_array = array();
    foreach ($array as $item) {
      array_push($new_array, $item[$key]);
    }
    return $new_array;
  }

  // Get local items like Gdrive or all attributes
  function getLocal($drivelike = false) {
    $more = $drivelike ? "" : ", hierarchy, parent_id, comment, img_path";
    $contents = DB::select('
      select name, type, path, filename, extension, timestamp, mimetype, size, dirname, basename'.$more.' from realisations
    ');
    $contents = json_decode(json_encode($contents), true); // stdClass to array
    return $contents;
  }

  // Get drive items
  function getDrive($rootDirectoryBasename)
  {
    $dir = '/';
    $recursive = true; // Get subdirectories also?
    $contents = collect(Storage::disk('google')->listContents($rootDirectoryBasename, $recursive));
    $contents = $contents->toArray();
    return $contents;
  }


  // Save drive file in storage, return $imgpath
  function saveDriveImageToLocal($storage_folder, $g_file_path, $name, $quality=80, $width=1280, $height=1280) {
    $img_storage_path = $storage_folder.$name; // path of the image          
    $absolute_path = "gs://".env('GOOGLE_STORAGE_BUCKET')."/".$img_storage_path;


    if (!file_exists($absolute_path)) { // Download image
      Log::info("Downloading image...");
      $readStream = Storage::disk('google')->getDriver()->readStream($g_file_path); // Get
      
      // Compress
      $image = new Imagick();
      $image->readImageBlob(stream_get_contents($readStream));

      $image->resizeImage($width, $height, Imagick::FILTER_LANCZOS, 1, true);
      $image->setImageCompressionQuality($quality);
      $image->stripImage();
      

      file_put_contents($absolute_path, $image); // Save

    }

    return $img_storage_path;
  }





  function syncRealisations() {
    set_time_limit(1000);

    Log::info("syncRealisations");
    Log::info("Init (cloud storage, database info, drive info)...");

    $storage = initGoogleStorage();

    // INIT PATH
    $storage_folder = "realisations/";

    // INIT LOCAL
    $local = self::getLocal(true);
    $local_basename = self::extract_key($local, 'basename');
    $local_map = self::map_with($local, 'basename');

    // INIT DRIVE
    $drive = self::getDrive(Config::get('constants.drive.realisations'));
    $drive_basename = self::extract_key($drive, 'basename');
    // $drive_map = self::map_with($drive, 'basename');

    $lines = DB::select('select path, timestamp from realisations');
    $lines = json_decode(json_encode($lines), true);
    $realisations_db = self::map_with($lines, 'path');


    Log::info("Delete files...");
    // CLEAN DRIVE
    $to_del_basename = self::flip_isset_diff($local_basename, $drive_basename); // init del array
    foreach ($to_del_basename as $basename) {
      $deleted = DB::delete('delete from realisations where basename=?', [ $basename ]); // Delete file from table
      unlink("gs://".env('GOOGLE_STORAGE_BUCKET')."/".$storage_folder.$basename.".".$local_map[$basename]['extension']); // Delete image from storage
      Log::info("Deleted ".$storage_folder.$basename.".".$local_map[$basename]['extension']);
    }


    Log::info("Update local");
    //UPDATE LOCAL
    $image_used = array();


    $it = 0;
    $nb = count($drive);
    $log_msg = "";
    foreach ($drive as $file) {
      $it++;
      $log_msg = $it."/".$nb." : ";

      $hierarchy = count(explode('/', $file['path'])) - 1; // 1=category 2=album 3=image
      $comment = "";
      $img_path = "";

      if (array_key_exists('mimetype', $file)) {

        // --IMG_PATH--
        // If image download image and initialize $img_path
        if (in_array($file['mimetype'], array("image/jpeg", "image/png", "image/gif", "image/bmp"))) { // It's an image
          $save_name=$file['basename'].".".$file['extension'];
          array_push($image_used, $save_name);
          $img_path = self::saveDriveImageToLocal($storage_folder, $file['path'], $save_name);
          $log_msg .= "image";
        }

        // --COMMENT--
        // If google doc in an ablum --> initialize $comment
        else if (($file['mimetype'] == 'application/vnd.google-apps.document') && $hierarchy == 3) {
          $service = Storage::disk('google')->getAdapter()->getService();
          $export = $service->files->export($file['basename'], 'text/plain', array('alt' => 'media' ));
          $comment = $export->getBody()->getContents();
          $comment = str_replace("\r\n\r\n", "\r\n", $comment);
          $log_msg .= "document";
        }

        else {
          $log_msg .= "autre";
        }

      } else {
        $log_msg .= "dossier";
      }


      $log_msg .= " => ";

      if (array_key_exists($file['path'], $realisations_db) && $realisations_db[$file['path']]['timestamp'] == $file['timestamp'])
      {
        $log_msg .= "nothing";
        Log::info($log_msg);
        continue;
      }

      // --PARENT ID--
      $parent_id = 0;
      if ($hierarchy > 1) { // set "category id to album" or "album id to image"
        $parent_id = DB::select('select id from realisations where path=?',[ $file['dirname'] ])[0]->id;
      }


      

      


      // Insert or upadte : folders, pictures, Gdoc
      if (in_array($file['basename'], $local_basename)) { // update
        
          DB::update('update realisations set name=?, type=?, path=?, filename=?, extension=?, timestamp=?, mimetype=?, size=?, dirname=?, hierarchy=?, parent_id=?, comment=?, img_path=? where basename=?', 
          [ $file['name'], $file['type'], $file['path'], $file['filename'], $file['extension'], $file['timestamp'], 
          array_key_exists('mimetype', $file) ? $file['mimetype'] : '',
          $file['size'], $file['dirname'], $hierarchy, $parent_id, $comment, $img_path, $file['basename'] ]);
          $log_msg .= "update";

      } else { // insert
        DB::insert('insert into realisations (name, type, path, filename, extension, timestamp, mimetype, size, dirname, basename, hierarchy, parent_id, comment, img_path) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
        [ $file['name'], $file['type'], $file['path'], $file['filename'], $file['extension'], $file['timestamp'], 
        array_key_exists('mimetype', $file) ? $file['mimetype'] : '',
        $file['size'], $file['dirname'], $file['basename'], $hierarchy, $parent_id, $comment, $img_path ]);
        $log_msg .= "insert";
      }

      Log::info($log_msg);

    } // end foreach


    // Check if images not used
    Log::info("Check and delete unuseful pics...");
    $files = getFiles($storage_folder);
    foreach ($files as $file) {
      $filename = pathinfo($file, PATHINFO_BASENAME);
      if (!in_array($filename, $image_used)) {
        Log::info("Delete image");
        unlink("gs://".env('GOOGLE_STORAGE_BUCKET')."/".$storage_folder.$filename); // Delete image from storage
      }
    }
    Log::info("DONE !");

    return response()->json(['result' => 'ok']);

  }



  // function syncTexts() {

  //   $service = Storage::disk('google')->getAdapter()->getService();
  //   $export = $service->files->export(Config::get("constants.drive.texts"), 'text/csv',  array('alt' => 'media' )); // DEFINIR CONSTANTES
  //   $content = $export->getBody()->getContents();
    
  //   // Convert to array
  //   $lines = preg_split('/\r\n/', $content); // '/\n|\r\n?/'
  //   $csv = array();
  //   foreach ($lines as $lines) {
  //       array_push($csv, str_getcsv($lines));
  //   }
  //   array_shift($csv);

  //   $deleted = DB::delete('delete from texts'); // Delete all from table
  //   foreach ($csv as $row) {
  //       DB::insert('insert into texts (page, context, id, value) values (?, ?, ?, ?)',
  //       [ $row[0], $row[1], $row[2], $row[3] ]);
  //   }

  // }



  function syncPictures() {

    set_time_limit(1000);

    Log::info("syncPictures");

    $storage = initGoogleStorage();

    $storage_folder = "img/";

    Log::info("Delete all lines in database");
    $deleted = DB::delete('delete from pictures'); // Delete all from table

    Log::info("Init drive info");
    // PAGE D'ACCUEIL
    $home = self::getDrive(Config::get('constants.drive.images'));
    usort($home, function($a, $b)
    {
        return strcmp($a['name'], $b['name']);
    });

    Log::info("Update all image in database and download image if needed");
    $it = 0;
    $nb = count($home);
    $log_msg = "";
    foreach ($home as $file) { // ATTENDRE DE FAIRE LA FONCTION SAVEDRIVE

      $it++;
      $log_msg = $it."/".$nb." : ";

      if (array_key_exists('mimetype', $file)) {
        if (in_array($file['mimetype'], array("image/jpeg", "image/png", "image/gif", "image/bmp"))) { // It's an image

          $explode = explode("-", $file['filename'], 5);
          $page = $explode[0];
          $context = $explode[1];
          $id = $explode[2];
          $alt = $explode[3];

          $save_name=$file['basename'].".".$file['extension'];

          $quality=80; $width=1280; $height=1280;

          switch ($page."-".$context) {
            case 'accueil-background':
              $quality=90;
              break;

            case 'activites-provider':
              $quality=70;
              $height=100;
              break;

            case 'activites-skill_1':
              $width=720; $height=720;
              $quality=50;
              break;

            case 'activites-skill_2':
              $width=720; $height=720;
              $quality=50;
              break;
            
            default:
              # code...
              break;
          }
          $log_msg .= $save_name;
          $img_path = self::saveDriveImageToLocal($storage_folder, $file['path'], $save_name, $quality, $width, $height);

          $log_msg .= " - insert";
          DB::insert('insert into pictures (page, context, id, img_path, alt) values (?, ?, ?, ?, ?)',
          [ $page, $context, $id, $img_path, $alt ]);

          Log::info($log_msg);
         
        }  
      }
    }
    Log::info("DONE !");

    return 'ok';

  }






    public function home()
    {
        if (url()->current() == url('admin')) {
          return redirect()->route('admin.home');
        }
        return view('admin.home', ['pageTitle' => 'Home']);
    }




    public function updateText(Request $request)
    {
      $value = $request->input('value');
      $page = $request->input('page');
      $context = $request->input('context');
      $id = $request->input('id');

      $update = DB::update('update texts set value = ? where page = ? and context = ? and id = ?', [ $value, $page, $context, $id ]);
      return response()->json(['success'=>array($request->all(), $update)]);
    }

}
