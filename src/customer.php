<?php

use Symfony\Component\HttpFoundation\Request;

// Controladores relacionados con la parte de administración del sitio web
$customer = $app['controllers_factory'];

// Nuevo cliente
$customer->match('/new', function (Request $request) use ($app) {
	if ($request->getMethod() == 'POST') {
	    $data = $request->request->all();
	    $data['created'] = date('Y-m-d H:i:s');
	    $app['db']->insert('customers', $data);
	}

    return $app['twig']->render('customer/new.html.twig');
})
->bind('customer_new')
->method('GET|POST');

// Editar cliente
$customer->get('/edit/:customerId', function () use ($app) {
    return $app['twig']->render('customer/edit.html.twig');
})
->bind('customer_edit');

// Ver cliente
$customer->get('/show/:customerId', function () use ($app) {
    return $app['twig']->render('customer/show.html.twig');
})
->bind('customer_show');

// Clientes
$customer->get('/{page}', function ($page) use ($app) {
    $customers = $app['db']->fetchAll('SELECT * FROM customers');

    return $app['twig']->render('customer/list.html.twig', array('entities' => $customers));
})
->assert('page', '\d+')
->value('page', 1)
->bind('customer_list');


return $customer;