<?php
require __DIR__ . '/vendor/autoload.php';

use Ivi\Core\Bootstrap\App;
use Ivi\Core\View\View;
use Ivi\Http\Request;

$app = new App(__DIR__);

$app->router->get('/', function () {
  return View::make('product/home', [
    'title' => 'Welcome to ivi.php!',
    'message' => 'Your minimalist PHP framework.'
  ]);
});

$app->router->post('/contact', function (Request $req) {
  $data = $req->json();
  return View::make('contact/thanks', [
    'name' => $data['name'] ?? 'Anonymous'
  ]);
});

$app->run();
