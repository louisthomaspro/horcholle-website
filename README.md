# horcholle.fr

* Laravel 5.7
* Google App Engine
* Travis CI
* Google Drive API
* VueJS

---

**Sources :**

* Laravel & Google Drive --> https://github.com/ivanvermeyen/laravel-google-drive-demo
* Run laravel on GAE --> https://cloud.google.com/community/tutorials/run-laravel-on-appengine-standard
* GAE & Travis CI --> https://cloud.google.com/solutions/continuous-delivery-with-travis-ci
* Static files on GAE --> https://cloud.google.com/appengine/docs/standard/php7/serving-static-files
* Google Storage --> https://cloud.google.com/appengine/docs/standard/php/googlestorage/
* Cloud Sql Proxy --> https://cloud.google.com/sql/docs/mysql/connect-admin-proxy

---

**Important commands :**


``` bash
tar -czf credentials.tar.gz secrets app.yaml .env
travis login --pro
travis encrypt-file credentials.tar.gz --pro --add
```

``` bash
${HOME}/google-cloud-sdk/cloud_sql_proxy -instances=horcholle:europe-west1:horcholle-instance-prod=tcp:3307 -credential_file=secrets/travis-cloud-sql-access.json &
```