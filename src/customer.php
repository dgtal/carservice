<?php

use Symfony\Component\HttpFoundation\Request;

// Controladores relacionados con la parte de administración del sitio web
$customer = $app['controllers_factory'];

// Nuevo cliente
$customer->match('/new', function (Request $request) use ($app) {
	if ($request->isMethod('POST')) {
	    $data = $request->request->all();
	    $data['created'] = date('Y-m-d H:i:s');
	    $app['db']->insert('customers', $data);
	}

    return $app['twig']->render('customer/new.html.twig');
})
->bind('customer_new')
->method('GET|POST');

// Editar cliente
$customer->match('/edit/{customerId}', function (Request $request, $customerId) use ($app) {
	if ($request->isMethod('POST')) {
		$data = $request->request->all();
	    $app['db']->update('customers', $data, array('id' => $customerId));
	}

    $customer = $app['db']->fetchAssoc('SELECT c.* FROM customers c WHERE id = ?', [(int) $customerId]);

	return $app['twig']->render('customer/edit.html.twig', array('entity' => $customer));
})
->assert('customerId', '\d+')
->bind('customer_edit')
->method('GET|POST');

// Ver cliente
$customer->get('/show/{customerId}', function ($customerId) use ($app) {
    return $app['twig']->render('customer/show.html.twig');
})
->assert('customerId', '\d+')
->bind('customer_show');

// Eliminar cliente
$customer->get('/delete/{customerId}', function ($customerId) use ($app) {
	$app['db']->delete('customers', array('id' => $customerId));
	// eliminar vehiculos asociados
	// eliminar entradas a taller
	// ...
	return $app->redirect('/customer');
})
->bind('customer_del');

// Clientes
$customer->get('/{page}', function ($page) use ($app) {
    $customers = $app['db']->fetchAll('SELECT c.* FROM customers c');

    return $app['twig']->render('customer/list.html.twig', array('entities' => $customers));
})
->assert('page', '\d+')
->value('page', 1)
->bind('customer_list');

// Clientes búsqueda
$customer->get('/search', function (Request $request) use ($app) {
	$customers = array();

	if ($request->get('s') !== '') {
	    //$customers = $app['db']->fetchAll('SELECT c.id, c.firstname || " " || c.lastname AS name FROM customers c WHERE c.firstname LIKE ?', ['%'.$request->get('s').'%']);

	    $customers = $app['db']->fetchAll('SELECT c.id, c.firstname, c.lastname
	    									FROM customers c
	    									WHERE c.firstname LIKE ?
	    									OR c.lastname LIKE ?', ['%'.$request->get('s').'%', '%'.$request->get('s').'%']);
	}

    return $app->json($customers);
})
->bind('customer_search');


return $customer;