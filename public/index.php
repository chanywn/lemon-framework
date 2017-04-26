<?php
require '../vendor/import.php';

import::module('route');
import::module('log');
//import::module('db');


route::get('/', function($request, $response) {
	return $response->view('welcome');
});

route::get('/404', function($request, $response) {
	return $response->statusCode(404);
});

route::run();