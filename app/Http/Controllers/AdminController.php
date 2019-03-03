<?php

namespace App\Http\Controllers;

use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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


    // Get drive items
    function getDrive($rootDirectoryBasename)
    {
        $dir = '/';
        $recursive = true; // Get subdirectories also?
        $contents = collect(Storage::disk('google')->listContents($rootDirectoryBasename, $recursive));
        $contents = $contents->toArray();
//        dd($contents);
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

            Log::info('Saving to '.$absolute_path.'...');
            file_put_contents($absolute_path, $image); // Save

        }

        return $img_storage_path;
    }


    function stripAccents($str)
    {
        $unwanted_array = array('Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U',
            'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
            'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y');
        return strtr($str, $unwanted_array);
    }

    function stripWhitespace($stripWhitespaces)
    {
        return preg_replace('/\s/', '-', $stripWhitespaces);
    }




    function syncRealisations()
    {
        set_time_limit(1000);

        Log::info("syncRealisations");

        // INIT PATH
        $storage_folder = "realisations/";

        initGoogleStorage();
        $datastore = initGoogleDatastore();

        // INIT DRIVE
        $drive = self::getDrive(Config::get('constants.drive.realisations'));
        // Add hierarchy key (depth in drive)
        foreach ($drive as $key => $value) {
            $drive[$key]['hierarchy'] = count(explode('/', $value['path'])) - 1;
        }

        // Sort by hierarchy
        uasort($drive, function ($a, $b) {
            return $a['hierarchy'] > $b['hierarchy'];
        });

        $categories = [];
        $albums = [];

        foreach ($drive as $file) {

            // Get all information
            $file['hierarchy'] = count(explode('/', $file['path'])) - 1; // 1=category 2=album 3=image
            $explode = explode("/", $file['dirname']);
            $file['parent_basename'] = end($explode);
            // --SORT--
            $endSortValue = strspn($file['name'], "0123456789");
            $sortValue = substr($file['name'], 0, $endSortValue);
            if ($sortValue == "") { // if there is no number at the begin of the filename
                $sortValue = 999;
            } else {
                $file['name'] = substr($file['name'], $endSortValue + 1);
            }
            $file['sort'] = (int)$sortValue;
            $file['url_friendly'] = preg_replace('/-+/', '-', $this->stripWhitespace($this->stripAccents(strtolower($file['name']))));


            if ($file['hierarchy'] == 1 && $file['type'] == 'dir') { // BUILD CATEGORY
                $key = $datastore->key('Category', $file['basename']);
                $categories[$file['basename']] = $datastore->entity($key, [
                    'name' => $file['name'],
                    'url_friendly' => $file['url_friendly'],
                    'sort' => $file['sort'],
                    'thumbnail' => '',
                    'albums' => array()
                ]);

            } else if ($file['hierarchy'] == 2) { // BUILD THUMBNAIL & ALBUMS

                $explode = explode("/", $file['dirname']);
                $album_basename = end($explode);

                // ALBUM
                if ($file['type'] == 'dir') {
                    $tmpAlbum = array(
                        'name' => $file['name'],
                        'desc' => '',
                        'sort' => $file['sort'],
                        'images' => array()
                    );
                    $albums[$album_basename][$file['basename']] = $tmpAlbum;
                } // THUMBNAIL
                else if (array_key_exists('mimetype', $file) && in_array($file['mimetype'], array("image/jpeg", "image/png", "image/gif", "image/bmp"))) {
                    $save_name = $file['basename'] . "." . $file['extension'];
                    $tmpThumbnail = array(
                        "name" => $file['name'],
                        "img_path" => self::saveDriveImageToLocal($storage_folder, $file['path'], $save_name)
                    );
                    $categories[$album_basename]['thumbnail'] = $tmpThumbnail;
                }


            } else if ($file['hierarchy'] == 3) { // IMAGES & DESCRIPTION


                if (array_key_exists('mimetype', $file)) {
                    $explode = explode("/", $file['dirname']);
                    $album_basename = end($explode);
                    $category_basename = $explode[count($explode) - 2];

                    // IMAGE
                    if (in_array($file['mimetype'], array("image/jpeg", "image/png", "image/gif", "image/bmp"))) {
                        $save_name = $file['basename'] . "." . $file['extension'];
                        $tmpImage = array(
                            "name" => $file['name'],
                            "sort" => $file['sort'],
                            "img_path" => self::saveDriveImageToLocal($storage_folder, $file['path'], $save_name)
                        );
                        array_push($albums[$category_basename][$album_basename]['images'], $tmpImage);

                    } // DESCRIPTION
                    else if ($file['mimetype'] == 'application/vnd.google-apps.document') {
                        $service = Storage::disk('google')->getAdapter()->getService();
                        $export = $service->files->export($file['basename'], 'text/plain', array('alt' => 'media'));
                        $comment = $export->getBody()->getContents();
                        $albums[$album_basename]['desc'] = str_replace("\r\n\r\n", "\r\n", $comment);
                    }
                }

            }

        }

        $categoriesAdd = [];

        foreach ($categories as $keyCategory => $category) {
            $tmpAlbums = $albums[$keyCategory];

            //remove keys
            $new_array = array();
            foreach ($tmpAlbums as $value) {
                $new_array[] = $value;
            }
            $tmpAlbums = $new_array;

            // sort albums
            uasort($tmpAlbums, function ($a, $b) {
                return $a['sort'] > $b['sort'];
            });
            foreach ($tmpAlbums as $keyAlbum => $tmpAlbum) {
                $tmpImages = $tmpAlbum['images'];
                uasort($tmpImages, function ($a, $b) {
                    return $a['sort'] > $b['sort'];
                });
                $tmpAlbums[$keyAlbum]['images'] = $tmpImages;
            }

            $category['albums'] = $tmpAlbums;

            $categoriesAdd[] = $category;
        }

        $this->clearDatastoreKind('Category');
        $datastore->insertBatch($categoriesAdd);

        Cache::forget('categories_dd');


        Log::info("DONE !");

        return response()->json(['result' => 'ok']);

    }





    function syncPictures()
    {

        set_time_limit(1000);

        Log::info("syncPictures");

        initGoogleStorage();
        $datastore = initGoogleDatastore();

        $storage_folder = "img/";

        $imagesFolder = Config::get('constants.drive.images');


        $pictureAdd = [];

        foreach ($imagesFolder as $keyName => $folder_id) {

            $drive = self::getDrive($folder_id);

//            dd($drive);

            foreach ($drive as $file) {


                // si c'est une image, on traite
                if (array_key_exists('mimetype', $file)) {
                    if (in_array($file['mimetype'], array("image/jpeg", "image/png", "image/gif", "image/bmp"))) { // It's an image

                        $picture = [];

                        // --SORT--
                        $picture['name'] = $file['name'];
                        $endSortValue = strspn($file['name'], "0123456789");
                        $sortValue = substr($file['name'], 0, $endSortValue);
                        if ($sortValue == "") { // if there is no number at the begin of the filename
                            $sortValue = 999;
                        } else {
                            $picture['name'] = substr($file['name'], $endSortValue + 1);
                        }
                        $picture['sort'] = (int)$sortValue;


                        $save_name = $file['basename'] . "." . $file['extension'];


                        $quality = 80;
                        $width = 1280;
                        $height = 1280;

                        Log::info($keyName);

                        switch ($keyName) {
                            case "accueil":
                                $picture['page'] = 'accueil';
                                $picture['context'] = 'background';
                                $quality = 90;
                                break;
                            case "activite1":
                                $picture['page'] = 'activites';
                                $picture['context'] = 'activite1';
                                $width = 720;
                                $height = 720;
                                $quality = 50;
                                break;
                            case "activite2":
                                $picture['page'] = 'activites';
                                $picture['context'] = 'activite2';
                                $width = 720;
                                $height = 720;
                                $quality = 50;
                                break;
                            case "fournisseurs":
                                $picture['page'] = 'activites';
                                $picture['context'] = 'fournisseurs';
                                $quality = 70;
                                $height = 100;
                                break;
                            case "presentation":
                                $picture['page'] = 'presentation';
                                $picture['context'] = 'equipe';
                                break;
                            case "fournisseurs-photos":
                                $picture['page'] = 'activites';
                                $picture['context'] = 'fournisseurs-photos';
                                break;
                            default:
                                break;
                        }


                        $picture['img_path'] = self::saveDriveImageToLocal($storage_folder, $file['path'], $save_name, $quality, $width, $height);

                        $key = $datastore->key('Picture');
                        $pictureAdd[] = $datastore->entity($key, $picture);


                    }
                }

            } // end foreach file


        } // end foreach folder

        $this->clearDatastoreKind('Picture');
        $datastore->insertBatch($pictureAdd);

        Cache::flush();

        Log::info("DONE !");

        return response()->json(['result' => 'ok']);

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

        $value = str_replace('|', ' ', $request->input('value'));
        Log::info($value);
        $page = $request->input('page');
        $context = $request->input('context');
        $id = $request->input('id');

        $datastore = initGoogleDatastore();

        $query = $datastore->query()
            ->kind('Text')
            ->filter('page', '=', $page)
            ->filter('context', '=', $context)
            ->filter('id', '=', $id);

        $result = $datastore->runQuery($query)->current();
        if (is_null($result)) {
            $key = $datastore->key('Text');
            $result = $datastore->entity($key, [
                'page' => $page,
                'context' => $context,
                'id' => $id
            ]);
        }

        $result['value'] = $value;

        $datastore->upsert($result);
        Cache::forget('info-' . $page);
    }

}
