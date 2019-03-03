<?php


if (!function_exists('bucket_url')) {

    function bucket_url($url)
    {
        return "https://storage.googleapis.com/" . env('GOOGLE_STORAGE_BUCKET') . "/" . $url;
    }

}


if (!function_exists('initGoogleStorage')) {

    function initGoogleStorage()
    {
        // GOOGLE BUCKET
        // https://googleapis.github.io/google-cloud-php/#/docs/google-cloud/v0.93.0/storage/storageclient
        $storage = new Google\Cloud\Storage\StorageClient([
            'projectId' => "horcholle",
            'keyFilePath' => base_path() . '/secrets/horcholle-storage-access.json'
        ]);
        $storage->registerStreamWrapper();

        return $storage;
    }

}


if (!function_exists('initGoogleDatastore')) {

    function initGoogleDatastore()
    {
        // GOOGLE DATASTORE
        // https://github.com/googleapis/google-cloud-php#google-cloud-datastore-ga
            $datastore = new Google\Cloud\Datastore\DatastoreClient([
                'keyFilePath' => base_path() . '/secrets/horcholle-datastore-access.json'
        ]);
        return $datastore;
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
