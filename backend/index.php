<?php
// backend/index.php

// Set content type to JSON
header("Content-Type: application/json; charset=UTF-8");

// Allow requests from any origin (in production, specify your frontend domain)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include core configuration
require_once 'api/config/core.php';

// Include JWT handler
require_once 'api/config/jwt.php';

// Include base model
require_once 'api/models/Database.php';

// Include controllers
require_once 'api/controllers/AuthController.php';
require_once 'api/controllers/SettingsController.php';
require_once 'api/controllers/ServicesController.php';
require_once 'api/controllers/ProjectsController.php';
require_once 'api/controllers/MessagesController.php';
require_once 'api/controllers/UsersController.php';
require_once 'api/controllers/AboutController.php';
require_once 'api/controllers/GalleryController.php';
require_once 'api/controllers/TestimonialsController.php';
require_once 'api/controllers/TeamMembersController.php';

// Include utility class
require_once 'api/utils/UploadHandler.php';

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Get request URI and parse it
$request_uri = $_SERVER['REQUEST_URI'];
$parsed_url = parse_url($request_uri);
$path = $parsed_url['path'];

// Remove the base path (assuming API is served from /api/)
$base_path = '/infinity/backend'; // Adjust this based on your server configuration
if (strpos($path, $base_path) === 0) {
    $path = substr($path, strlen($base_path));
}

// Route the request
try {
    // Define routes
    $routes = [
        // Authentication routes
        '/api/login' => ['controller' => 'AuthController', 'method' => 'login'],
        
        // Settings routes
        '/api/settings' => [
            'GET' => ['controller' => 'SettingsController', 'method' => 'getSettings'],
            'PUT' => ['controller' => 'SettingsController', 'method' => 'updateSettings']
        ],
        
        // Services routes
        '/api/services' => [
            'GET' => ['controller' => 'ServicesController', 'method' => 'getAll'],
            'POST' => ['controller' => 'ServicesController', 'method' => 'create']
        ],
        '/api/services/(\d+)' => [
            'GET' => ['controller' => 'ServicesController', 'method' => 'getById'],
            'PUT' => ['controller' => 'ServicesController', 'method' => 'update'],
            'DELETE' => ['controller' => 'ServicesController', 'method' => 'delete']
        ],
        
        // Projects routes
        '/api/projects' => [
            'GET' => ['controller' => 'ProjectsController', 'method' => 'getAll'],
            'POST' => ['controller' => 'ProjectsController', 'method' => 'create']
        ],
        '/api/projects/(\d+)' => [
            'GET' => ['controller' => 'ProjectsController', 'method' => 'getById'],
            'PUT' => ['controller' => 'ProjectsController', 'method' => 'update'],
            'DELETE' => ['controller' => 'ProjectsController', 'method' => 'delete']
        ],
        
        // Messages routes
        '/api/messages' => [
            'GET' => ['controller' => 'MessagesController', 'method' => 'getAll'],
            'POST' => ['controller' => 'MessagesController', 'method' => 'create']
        ],
        '/api/messages/(\d+)' => [
            'GET' => ['controller' => 'MessagesController', 'method' => 'getById'],
            'PUT' => ['controller' => 'MessagesController', 'method' => 'updateStatus'],
            'DELETE' => ['controller' => 'MessagesController', 'method' => 'delete']
        ],
        
        // Users routes (admin only)
        '/api/users' => [
            'GET' => ['controller' => 'UsersController', 'method' => 'getAll'],
            'POST' => ['controller' => 'UsersController', 'method' => 'create']
        ],
        '/api/users/(\d+)' => [
            'GET' => ['controller' => 'UsersController', 'method' => 'getById'],
            'PUT' => ['controller' => 'UsersController', 'method' => 'update'],
            'DELETE' => ['controller' => 'UsersController', 'method' => 'delete']
        ],
        
        // About routes
        '/api/about' => [
            'GET' => ['controller' => 'AboutController', 'method' => 'getAbout'],
            'PUT' => ['controller' => 'AboutController', 'method' => 'updateAbout']
        ],
        
        // Gallery routes
        '/api/gallery' => [
            'GET' => ['controller' => 'GalleryController', 'method' => 'getAll'],
            'POST' => ['controller' => 'GalleryController', 'method' => 'create']
        ],
        '/api/gallery/category/([^/]+)' => [
            'GET' => ['controller' => 'GalleryController', 'method' => 'getByCategory']
        ],
        '/api/gallery/upload' => [
            'POST' => ['controller' => 'GalleryController', 'method' => 'uploadImage']
        ],
        '/api/gallery/sort' => [
            'PUT' => ['controller' => 'GalleryController', 'method' => 'updateSortOrder']
        ],
        '/api/gallery/(\d+)' => [
            'GET' => ['controller' => 'GalleryController', 'method' => 'getById'],
            'PUT' => ['controller' => 'GalleryController', 'method' => 'update'],
            'DELETE' => ['controller' => 'GalleryController', 'method' => 'delete']
        ],
        
        // Testimonials routes
        '/api/testimonials' => [
            'GET' => ['controller' => 'TestimonialsController', 'method' => 'getAll'],
            'POST' => ['controller' => 'TestimonialsController', 'method' => 'create']
        ],
        '/api/testimonials/active' => [
            'GET' => ['controller' => 'TestimonialsController', 'method' => 'getActive']
        ],
        '/api/testimonials/upload' => [
            'POST' => ['controller' => 'TestimonialsController', 'method' => 'uploadImage']
        ],
        '/api/testimonials/(\d+)' => [
            'GET' => ['controller' => 'TestimonialsController', 'method' => 'getById'],
            'PUT' => ['controller' => 'TestimonialsController', 'method' => 'update'],
            'DELETE' => ['controller' => 'TestimonialsController', 'method' => 'delete']
        ],
        
        // Team Members routes
        '/api/team' => [
            'GET' => ['controller' => 'TeamMembersController', 'method' => 'getAll'],
            'POST' => ['controller' => 'TeamMembersController', 'method' => 'create']
        ],
        '/api/team/active' => [
            'GET' => ['controller' => 'TeamMembersController', 'method' => 'getActive']
        ],
        '/api/team/upload' => [
            'POST' => ['controller' => 'TeamMembersController', 'method' => 'uploadImage']
        ],
        '/api/team/sort' => [
            'PUT' => ['controller' => 'TeamMembersController', 'method' => 'updateSortOrder']
        ],
        '/api/team/(\d+)' => [
            'GET' => ['controller' => 'TeamMembersController', 'method' => 'getById'],
            'PUT' => ['controller' => 'TeamMembersController', 'method' => 'update'],
            'DELETE' => ['controller' => 'TeamMembersController', 'method' => 'delete']
        ]
    ];

    $matched_route = false;
    
    foreach ($routes as $route_pattern => $route_config) {
        // Check if route has method-specific configuration
        if (isset($route_config['GET']) || isset($route_config['POST']) || isset($route_config['PUT']) || isset($route_config['DELETE'])) {
            // This route has method-specific configuration
            if (isset($route_config[$method])) {
                if (preg_match('#^' . $route_pattern . '$#', $path, $matches)) {
                    $controller_info = $route_config[$method];
                    $matched_route = true;
                    
                    // Check if route has parameters (like /api/services/123)
                    if (count($matches) > 1) {
                        $params = array_slice($matches, 1); // Get captured groups
                        $controller = new $controller_info['controller']();
                        $controller->{$controller_info['method']}($params);
                    } else {
                        $controller = new $controller_info['controller']();
                        $controller->{$controller_info['method']}();
                    }
                    break;
                }
            }
        } else {
            // This route has a single configuration for all methods
            if (preg_match('#^' . $route_pattern . '$#', $path, $matches)) {
                $controller_info = $route_config;
                $matched_route = true;
                
                if (count($matches) > 1) {
                    $params = array_slice($matches, 1);
                    $controller = new $controller_info['controller']();
                    $controller->{$controller_info['method']}($params);
                } else {
                    $controller = new $controller_info['controller']();
                    $controller->{$controller_info['method']}();
                }
                break;
            }
        }
    }
    
    if (!$matched_route) {
        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error: ' . $e->getMessage()]);
}