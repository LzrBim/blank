<?php
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/environment.php
----------------------------------------------------------------------------- */  

//SET TWIG ENVIRONMENT
$twig = $app->view->getEnvironment();
$twig->addGlobal('ENVIRONMENT', 'local');