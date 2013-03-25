<?php

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

$app = new Application();
$app->register(new UrlGeneratorServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new ServiceControllerServiceProvider());
$app->register(new SecurityServiceProvider());
$app->register(new TwigServiceProvider(), array(
    'twig.path'    => array(__DIR__.'/../templates'),
    'twig.options' => array('cache' => __DIR__.'/../cache/twig'),
));

$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    // add custom globals, filters, tags, ...

    return $twig;
}));

// Base de datos
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_sqlite',
        'path'   => __DIR__.'/../config/carservicedb.sqlite',
    ),
));

// configuración de la seguridad
$app['security.encoder.digest'] = $app->share(function ($app) {
    // algoritmo SHA-1, con 1 iteración y sin codificar en base64
    return new MessageDigestPasswordEncoder('sha1', false, 1);
});

$app['security.firewalls'] = array(
    'admin' => array(
        'pattern' => '^/',
        'http'    => true,
        'users'   => array(
            // la contraseña sin codificar es "1234"
            'admin' => array('ROLE_ADMIN', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220'),
        ),
    ),
);

return $app;