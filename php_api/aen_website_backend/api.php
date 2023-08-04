<?php

/*
Run the API with:
php -S localhost:8000 index.php
php -S localhost:8000
*/

function getFileExtention($fileName) {
    $parsedFileName = explode('.', $fileName);
    if (count($parsedFileName) > 1) {
        return $parsedFileName[count($parsedFileName) - 1];
    } else {
        return '';
    }
}

function getImage( $imagesFolderPath, $folderName, $imageName) {
    $acceptedExtentions = ['jpg', 'jpeg', 'png'];
    $imagePath = $imagesFolderPath . '/' . $folderName . '/' . $imageName;
    $imageExtention = getFileExtention($imageName);

    if (!file_exists($imagePath)) {
        http_response_code(404);
        echo json_encode(array('detail' => 'Not found.'));
    } else if (!is_file($imagePath)) {
        http_response_code(422);
        echo json_encode(array('detail' => 'Not a file.'));
    } else if (!in_array(strtolower($imageExtention), $acceptedExtentions)) {
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

function listImagesInFolder($imagesFolderPath, $folderName) {
    $folderPath = $imagesFolderPath . '/' . $folderName;

    if (!file_exists($folderPath)) {
        http_response_code(404);
        echo json_encode(array('detail' => 'Not found.'));
    } else if (!is_dir($folderPath)) {
        http_response_code(422);
        echo json_encode(array('detail' => 'Not a folder.'));
    } else {
        $content = array_diff(scandir($folderPath), array('.', '..'));
        $imageNames = array_values(array_filter($content, function($imageName) {
            $acceptedExtentions = ['jpg', 'jpeg', 'png'];
            $imageExtention = getFileExtention($imageName);
            if (in_array(strtolower($imageExtention), $acceptedExtentions)){
                return $imageName;
            }
        }));
        http_response_code(200);
        echo json_encode($imageNames);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $imagesFolderPath = '/Users/aliemrenebiler/Documents/Coding/Web_Projects/images';

    $requestUri = $_SERVER['REQUEST_URI'];
    [$route, $query] = explode('?', $requestUri);

    // array_filter(): clears empty strings
    // array_values(): reindexes the array
    $parsedRoute = array_values(array_filter(explode('/', $route)));

    if (count($parsedRoute) === 2 && $parsedRoute[0] === 'images') {
        // Endpoint: /images/folder_name
        $folderName = $parsedRoute[1];
        listImagesInFolder($imagesFolderPath, $folderName);
    } else if (count($parsedRoute) === 3 && $parsedRoute[0] === 'images'){
        // Endpoint: /images/folder_name/image_name
        $folderName = $parsedRoute[1];
        $imageName = $parsedRoute[2];
        getImage($imagesFolderPath, $folderName, $imageName);
    } else {
        // Not existing endpoints
        http_response_code(404);
        echo json_encode(array('detail' => 'Not Found'));
    }
} else {
    http_response_code(405);
    echo json_encode(array('detail' => 'Method Not Allowed'));
}
