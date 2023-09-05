<?php

/*
Run the API with:
php -S localhost:8000 index.php
*/

$DEV_ENV_VARS = [
    "ACCESS-CONTROL-ALLOW-ORIGIN" => "*",
    "IMAGES_FOLDER_PATH" => "/Users/aliemrenebiler/Documents/Coding/Web_Projects/images",
    "ACCEPTED_IMAGE_FORMATS" => ["jpg","jpeg","png"],
];

$PROD_ENV_VARS = [
    "ACCESS-CONTROL-ALLOW-ORIGIN" => "https://www.aliemrenebiler.com",
    "IMAGES_FOLDER_PATH" => "/home/aliemren/images",
    "ACCEPTED_IMAGE_FORMATS" => ["jpg","jpeg","png"],
];

$ENV_VARS = $PROD_ENV_VARS;

function getFileExtention($fileName) {
    $parsedFileName = explode('.', $fileName);
    if (count($parsedFileName) > 1) {
        return $parsedFileName[count($parsedFileName) - 1];
    } else {
        return '';
    }
}

function getImage($folderName, $imageName) {
    $imagePath = $GLOBALS['ENV_VARS']['IMAGES_FOLDER_PATH'] . '/' . $folderName . '/' . $imageName;
    $imageExtention = getFileExtention($imageName);

    if (!file_exists($imagePath)) {
        http_response_code(404);
        echo json_encode(array('detail' => 'Not found.'));
    } else if (!is_file($imagePath)) {
        http_response_code(422);
        echo json_encode(array('detail' => 'Not a file.'));
    } else if (!in_array(strtolower($imageExtention), $GLOBALS['ENV_VARS']['ACCEPTED_IMAGE_FORMATS'])) {
        http_response_code(422);
        echo json_encode(array('detail' => 'Unaccepted format. Must be JPEG or PNG.'));
    } else {
        if (in_array(strtolower($imageExtention), ['jpg', 'jpeg'])) {
            header('Content-Type: image/jpeg');
        } else if (strtolower($imageExtention) === 'png') {
            header('Content-Type: image/png');
        }
        http_response_code(200);
        readfile($imagePath);
    }
}

function listImagesInFolder($folderName) {
    $folderPath = $GLOBALS['ENV_VARS']['IMAGES_FOLDER_PATH'] . '/' . $folderName;

    if (!file_exists($folderPath)) {
        http_response_code(404);
        echo json_encode(array('detail' => 'Not found.'));
    } else if (!is_dir($folderPath)) {
        http_response_code(422);
        echo json_encode(array('detail' => 'Not a folder.'));
    } else {
        $content = array_diff(scandir($folderPath), array('.', '..'));
        $imageNames = array_values(array_filter($content, function($imageName) {
            $imageExtention = getFileExtention($imageName);
            if (in_array(strtolower($imageExtention), $GLOBALS['ENV_VARS']['ACCEPTED_IMAGE_FORMATS'])){
                return $imageName;
            }
        }));
        http_response_code(200);
        echo json_encode($imageNames);
    }
}

// ----- Main Code -----

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header("Access-Control-Allow-Origin: " . $GLOBALS['ENV_VARS']['ACCESS-CONTROL-ALLOW-ORIGIN']);

    $requestUri = $_SERVER['REQUEST_URI'];
    $parsedRequestUri = explode('?', $requestUri);
    
    $route = $parsedRequestUri[0];
    if (count($parsedRequestUri) > 1) {
        $query = $parsedRequestUri[1];
    }
    
    // array_filter(): clears empty strings
    // array_values(): reindexes the array
    $parsedRoute = array_values(array_filter(explode('/', $route)));
    $imageRouteIndex = array_search('images', $parsedRoute, true);
    $parsedRoute = array_slice($parsedRoute, $imageRouteIndex);

    if (count($parsedRoute) === 2 && $parsedRoute[0] === 'images') {
        // Endpoint: /images/folder_name
        $folderName = $parsedRoute[1];
        listImagesInFolder($folderName);
    } else if (count($parsedRoute) === 3 && $parsedRoute[0] === 'images'){
        // Endpoint: /images/folder_name/image_name
        $folderName = $parsedRoute[1];
        $imageName = $parsedRoute[2];
        getImage($folderName, $imageName);
    } else {
        // Not existing endpoints
        http_response_code(404);
        echo json_encode(array('detail' => 'Not Found'));
    }
} else {
    http_response_code(405);
    echo json_encode(array('detail' => 'Method Not Allowed'));
}
