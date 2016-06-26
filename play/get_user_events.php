<?php
// modified from app_dev.php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

umask(0000);

$loader = require_once __DIR__ . '/app/bootstrap.php.cache';
Debug::enable();

require_once __DIR__ . '/app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$kernel->boot();

$container = $kernel->getContainer();
$container->enterScope('request');
$container->set('request', $request);

// all our setup is done!!!!!!

$em = $container->get('doctrine')
    ->getManager();

$wayne = $em
    ->getRepository('UserBundle:User')
    ->findOneByUsernameOrEmail('wayne');

foreach ($wayne->getEvents() as $event) {
    var_dump($event->getName());
}
