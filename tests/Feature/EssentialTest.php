<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Log;
use Storage;

class EssentialTest extends TestCase
{

    public function testHomepage()
    {
        $resp = $this->get('/accueil');
        $this->assertEquals('200', $resp->getStatusCode());
    }

    public function testDatastoreConnection()
    {
        $datastore = initGoogleDatastore();

        $key = $datastore->key('Test');
        $entity = $datastore->entity($key, [
            'name' => 'test'
        ]);
        $datastore->insert($entity);

        $query = $datastore->query()
            ->kind('Test');
        $result = $datastore->runQuery($query);
        $entitiesKey = [];
        foreach ($result as $entity) {
            $entitiesKey[] = $entity->key();
        }
        $datastore->deleteBatch($entitiesKey);
        $this->assertTrue(true, true);

    }

    public function testGoogleDrive()
    {
        $recursive = false; // Get subdirectories also?
        $contents = collect(Storage::disk('google')->listContents('/', $recursive));
        $contents = $contents->toArray();
        $this->assertTrue(count($contents) > 0);
    }

    public function testGoogleStorage()
    {
        $storage = initGoogleStorage();

        $storage_path = "gs://".env('GOOGLE_STORAGE_BUCKET')."/";
        $test_image_name = "test.jpg";
        $test_image_name_copy = "test_copy.jpg";
        $test_image = file_get_contents($storage_path.$test_image_name);
        file_put_contents($storage_path.$test_image_name_copy,$test_image);
        unlink($storage_path.$test_image_name_copy);      

        $this->assertTrue(true, true);
    }

    
}
