<?php

use Symfony\Component\HttpFoundation\Request;

// Controladores relacionados con la parte de administración del sitio web
$car = $app['controllers_factory'];

// Nuevo auto
$car->match('/new/{customerId}', function (Request $request, $customerId) use ($app) {
	if ($request->isMethod('POST')) {
	    $data = $request->request->all();
	    $data['created'] = date('Y-m-d H:i:s');
	    $app['db']->insert('cars', $data);
	}

    return $app['twig']->render('car/new.html.twig', array('customerId' => $customerId));
})
->bind('car_new')
->method('GET|POST');

// Ver auto
$car->match('/show/{carId}', function (Request $request, $carId) use ($app) {
})
->bind('car_show');

// Editar auto
$car->match('/edit/{carId}', function (Request $request, $carId) use ($app) {
})
->bind('car_edit');

// Eliminar auto
$car->match('/del/{carId}', function (Request $request, $carId) use ($app) {
})
->bind('car_del');

// Autos
$car->get('/{page}', function ($page) use ($app) {
    $cars = $app['db']->fetchAll('SELECT cr.*,
    									 cs.id as customer_id,
    									 cs.firstname as customer_firstname,
    									 cs.lastname as customer_lastname
    								FROM cars cr
    								INNER JOIN customers cs ON cs.id = cr.customer_id');

    return $app['twig']->render('car/list.html.twig', array('entities' => $cars));
})
->assert('page', '\d+')
->value('page', 1)
->bind('car_list');

// Autos de un cliente
$car->get('/customer/{customerId}', function (Request $request, $customerId) use ($app) {
    $cars = $app['db']->fetchAll('SELECT cars.id, cars.make, cars.model, cars.plate FROM cars WHERE cars.customer_id = ?', [(int) $customerId]);

    return $app->json($cars);
})
->assert('customerId', '\d+')
->bind('customer_cars');

return $car;