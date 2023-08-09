<?php

header('Access-Control-Allow-Origin: http://localhost:4200'); // Remplacez avec l'URL de votre application front-end
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Credentials: true'); // Activez ceci si vous avez besoin d'utiliser des cookies ou des identifiants d'authentification

use App\Kernel;

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
