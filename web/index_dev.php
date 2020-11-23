<?php

require '../bootstrap.php';
require '../MiniBlogApplication.php';

//デバックモードon
$app = new MiniBlogApplication(true);
$app->run();

