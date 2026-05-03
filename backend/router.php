<?php
// Router for PHP's built-in web server (use: php -S localhost:8000 router.php)
// This is a simple router that directs all requests to index.php
// Place this file in your CodeIgniter root and run: php -S localhost:8000 router.php

if (php_sapi_name() === 'cli-server') {
    $url = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;  // Serve the requested resource as-is.
    }
}

require_once __DIR__ . '/index.php';
