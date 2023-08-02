<?php

// Function to retrieve the image based on the provided folder name and image name
function getImage($folderName, $imageName) {
    // Sanitize input to avoid directory traversal
    $folderName = preg_replace('/[^a-zA-Z0-9_\-]/', '', $folderName);
    $imageName = preg_replace('/[^a-zA-Z0-9_\-.]/', '', $imageName);

    // Replace this with the actual base directory where your images are stored
    $baseDir = 'path/to/your/images/';

    // Check if the file exists
    $imagePath = $baseDir . $folderName . '/' . $imageName;
    if (file_exists($imagePath)) {
        // Return the image file with appropriate headers
        header('Content-Type: image/jpeg'); // Change the Content-Type as per your image type
        readfile($imagePath);
        exit;
    } else {
        // Image not found, return an error response
        http_response_code(404);
        echo json_encode(array('error' => 'Image not found'));
        exit;
    }
}

// Function to list all image names inside a folder
function listImagesInFolder($folderName) {
    // Sanitize input to avoid directory traversal
    $folderName = preg_replace('/[^a-zA-Z0-9_\-]/', '', $folderName);

    // Replace this with the actual base directory where your images are stored
    $baseDir = 'path/to/your/images/';

    // Check if the folder exists
    $folderPath = $baseDir . $folderName;
    if (is_dir($folderPath)) {
        $images = array_diff(scandir($folderPath), array('.', '..'));
        echo json_encode($images);
        exit;
    } else {
        // Folder not found, return an error response
        http_response_code(404);
        echo json_encode(array('error' => 'Folder not found'));
        exit;
    }
}

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Extract the endpoint from the URL
    $requestUri = $_SERVER['REQUEST_URI'];
    $baseApiPath = '/api/images/';
    $endpointPath = substr($requestUri, strpos($requestUri, $baseApiPath) + strlen($baseApiPath));

    // Check if the request is for listing images or retrieving a specific image
    if (strpos($endpointPath, '/') === false) {
        // Endpoint for listing images in a folder: /api/images/folder_name
        $folderName = $endpointPath;
        listImagesInFolder($folderName);
    } else {
        // Endpoint for retrieving a specific image: /api/images/folder_name/image_name.jpg
        list($folderName, $imageName) = explode('/', $endpointPath, 2);
        getImage($folderName, $imageName);
    }
} else {
    http_response_code(405);
    echo json_encode(array('error' => 'Method not allowed'));
    exit;
}
