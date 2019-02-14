<?php

namespace App\Http\Controllers;

use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Imagick;
use Storage;


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
    function flip_isset_diff($b, $a)
    { // very little bit faster
        $at = array_flip($a);
        $d = array();
        foreach ($b as $i)
            if (!isset($at[$i]))
                $d[] = $i;
        return $d;
    }

    // Map each $item in $array with $item[$key] key value
    function map_with($array, $key)
    {
        $array_with_keys = array_fill_keys(self::extract_key($array, $key), 'test');
        for ($i = 0; $i < count($array); $i++) {
            $key_value = $array[$i][$key];
            $array_with_keys[$key_value] = $array[$i];
        }
        return $array_with_keys;
    }

    // Extract all $item[$key] values from $array
    function extract_key($array, $key)
    {
        $new_array = array();
        foreach ($array as $item) {
            array_push($new_array, $item[$key]);
        }
        return $new_array;
    }

    // Get local items like Gdrive or all attributes
    function getLocal($drivelike = false)
    {
        $more = $drivelike ? "" : ", hierarchy, parent_id, comment, img_path";
        $contents = DB::select('
      select name, type, path, filename, extension, timestamp, mimetype, size, dirname, basename' . $more . ' from realisations
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

    function clearDatastoreKind($kind)
    {
        $datastore = initGoogleDatastore();
        $query = $datastore->query()
            ->kind($kind);
        $result = $datastore->runQuery($query);

        $entitiesKey = [];

        foreach ($result as $entity) {
            $entitiesKey[] = $entity->key();
        }

        $datastore->deleteBatch($entitiesKey);
    }


    // Save drive file in storage, return $imgpath
    function saveDriveImageToLocal($storage_folder, $g_file_path, $name, $quality = 80, $width = 1280, $height = 1280)
    {
        $img_storage_path = $storage_folder . $name; // path of the image
        $absolute_path = "gs://" . env('GOOGLE_STORAGE_BUCKET') . "/" . $img_storage_path;


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


    function syncRealisations()
    {
        set_time_limit(1000);

        // INIT PATH
        $storage_folder = "realisations/";


        $datastore = initGoogleDatastore();

        // INIT DRIVE
        $drive = self::getDrive(Config::get('constants.drive.realisations'));

        $this->clearDatastoreKind('Drive');

        $driveAdd = [];

        $it = 0;
        $nb = count($drive);
        foreach ($drive as $file) {
            $it++;
            Log::info($it . "/" . $nb);

            $file['hierarchy'] = count(explode('/', $file['path'])) - 1; // 1=category 2=album 3=image
            $file['parent_id'] = $file['dirname'];

            if (array_key_exists('mimetype', $file)) {

                // --IMG_PATH--
                // If image download image and initialize $img_path
                if (in_array($file['mimetype'], array("image/jpeg", "image/png", "image/gif", "image/bmp"))) { // It's an image
                    $save_name = $file['basename'] . "." . $file['extension'];
                    $file['img_path'] = self::saveDriveImageToLocal($storage_folder, $file['path'], $save_name);
                }

                // --COMMENT--
                // If google doc in an ablum --> initialize $comment
                else if (($file['mimetype'] == 'application/vnd.google-apps.document') && $file['hierarchy'] == 3) {
                    $service = Storage::disk('google')->getAdapter()->getService();
                    $export = $service->files->export($file['basename'], 'text/plain', array('alt' => 'media'));
                    $comment = $export->getBody()->getContents();
                    $file['img_comment'] = str_replace("\r\n\r\n", "\r\n", $comment);
                }

            }

            // --SORT--
            $endSortValue = strspn($file['filename'], "0123456789");
            $sortValue = substr($file['filename'], 0, $endSortValue);
            $file['filename'] = substr($file['filename'], $endSortValue + 1);
            if ($sortValue == "") { // if there is no number at the begin of the filename
                $sortValue = 999;
                $file['filename'] = $file['filename'];
            }
            $file['sort'] = $sortValue;


            $driveAdd[] = $datastore->entity($datastore->key('Drive', $file['basename']), $file);
        }

        $datastore->insertBatch($driveAdd);

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


    function syncPictures()
    {

        set_time_limit(1000);

        Log::info("syncPictures");

        $storage = initGoogleStorage();

        $storage_folder = "img/";

        Log::info("Delete all lines in database");
        $deleted = DB::delete('delete from pictures'); // Delete all from table

        Log::info("Init drive info");
        // PAGE D'ACCUEIL
        $home = self::getDrive(Config::get('constants.drive.images'));
        usort($home, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        Log::info("Update all image in database and download image if needed");
        $it = 0;
        $nb = count($home);
        $log_msg = "";
        foreach ($home as $file) { // ATTENDRE DE FAIRE LA FONCTION SAVEDRIVE

            $it++;
            $log_msg = $it . "/" . $nb . " : ";

            if (array_key_exists('mimetype', $file)) {
                if (in_array($file['mimetype'], array("image/jpeg", "image/png", "image/gif", "image/bmp"))) { // It's an image

                    $explode = explode("-", $file['filename'], 5);
                    $page = $explode[0];
                    $context = $explode[1];
                    $id = $explode[2];
                    $alt = $explode[3];

                    $save_name = $file['basename'] . "." . $file['extension'];

                    $quality = 80;
                    $width = 1280;
                    $height = 1280;

                    switch ($page . "-" . $context) {
                        case 'accueil-background':
                            $quality = 90;
                            break;

                        case 'activites-provider':
                            $quality = 70;
                            $height = 100;
                            break;

                        case 'activites-skill_1':
                            $width = 720;
                            $height = 720;
                            $quality = 50;
                            break;

                        case 'activites-skill_2':
                            $width = 720;
                            $height = 720;
                            $quality = 50;
                            break;

                        default:
                            # code...
                            break;
                    }
                    $log_msg .= $save_name;
                    $img_path = self::saveDriveImageToLocal($storage_folder, $file['path'], $save_name, $quality, $width, $height);

                    $log_msg .= " - insert";
                    DB::insert('insert into pictures (page, context, id, img_path, alt) values (?, ?, ?, ?, ?)',
                        [$page, $context, $id, $img_path, $alt]);

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

        $update = DB::update('update texts set value = ? where page = ? and context = ? and id = ?', [$value, $page, $context, $id]);
        return response()->json(['success' => array($request->all(), $update)]);
    }

}
