<?php

use Symfony\Component\HttpFoundation\Request;

// Controladores relacionados con la parte de administración del sitio web
$workshop = $app['controllers_factory'];

// Nueva entrada a taller
$workshop->match('/new', function (Request $request) use ($app) {
	if ($request->isMethod('POST')) {
	    $data = $request->request->all();

	    // Eliminamos campos que no precisamos
	    unset($data['customer']);
	    unset($data['customer_id']);

	    $data['done']      = 0;
	    $data['startdate'] = date('Y-m-d H:i:s');
	    $data['created']   = date('Y-m-d H:i:s');
	    $app['db']->insert('workshop', $data);
	}

    return $app['twig']->render('workshop/new.html.twig');
})
->bind('workshop_new')
->method('GET|POST');

// En taller
$workshop->get('/{page}', function ($page) use ($app) {
    $cars = $app['db']->fetchAll('SELECT wk.*, cr.*, cs.* FROM workshop wk JOIN cars cr ON cr.id = wk.car_id JOIN customers cs ON cr.customer_id = cs.id');

    return $app['twig']->render('workshop/list.html.twig', array('entities' => $cars));
})
->assert('page', '\d+')
->value('page', 1)
->bind('workshop_list');


return $workshop;