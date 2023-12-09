<?php
require '../autoload.php';
date_default_timezone_set('Asia/Tokyo');

$routes = include('../Routing/routes.php');
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = explode('/', $url)[1];
$method = $_SERVER['REQUEST_METHOD'];

if($path == 'png' || $path == 'jpeg' || $path == 'gif'){
    $path = 'getImage';
}

if (isset($routes[$path][$method])) {
    $renderer = $routes[$path][$method]();
    
    try {
        foreach ($renderer->getFields() as $name => $value) {
            $sanitized_value = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES);

            if ($sanitized_value && $sanitized_value === $value) {
                header("{$name}: {$sanitized_value}");
            } else {
                http_response_code(500);
                if ($DEBUG) print("Failed setting header - original: '$value', sanitized: '$sanitized_value'");
                exit;
            }

            print($renderer->getContent());
        }
    } catch (Exception $e) {
        http_response_code(500);
        print("Internal error, please contact the admin.<br>");
        if ($DEBUG) print($e->getMessage());
    }
} else {
    http_response_code(404);
    echo "404 Not Found: The requested route was not found on this server.";
}
