<?php
$response = ['success' => false, 'files' => []];

$folderPath = dirname(__DIR__) . '/images/';
if (is_dir($folderPath)) 
{
    $files = array_filter(scandir($folderPath), function ($file) use ($folderPath) 
    {
        $filePath = $folderPath . $file;
        return is_file($filePath) && preg_match('/\.(jpg|jpeg|png)$/i', $file);
    });

    $response['success'] = true;
    $response['files'] = array_values($files); // Zwracaj pliki w formie tablicy
} 
else 
{
    $response['message'] = 'Folder nie istnieje.';
}

header('Content-Type: application/json');
echo json_encode($response);
