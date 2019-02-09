<?php


if (!function_exists('bucket_url')) {

    function bucket_url($url)
    {
        return "https://storage.cloud.google.com/".env('GOOGLE_STORAGE_BUCKET')."/".$url;
    }

}


if (!function_exists('initGoogleStorage')) {

    function initGoogleStorage() {
        // GOOGLE BUCKET
        // https://googleapis.github.io/google-cloud-php/#/docs/google-cloud/v0.93.0/storage/storageclient
        $storage = new Google\Cloud\Storage\StorageClient([
          'projectId' => "horcholle",
          'keyFilePath' => base_path().'/horcholle-storage-access.json'
        ]);
        $storage->registerStreamWrapper();

        return $storage;
    }

}

if (!function_exists('getFiles')) {

 function getFiles($directory) // ex : "img/"
    {
      $storage = initGoogleStorage();

      $bucket = $storage->bucket(env('GOOGLE_STORAGE_BUCKET'));
      $options = ['prefix' => $directory];
      $objects = $bucket->objects($options);
      $objectsNames = array();
      foreach ($objects as $object) {
          if ($object->name() != $directory) {
            array_push($objectsNames, $object->name());
          }
      }

      return $objectsNames;
    
  }
}