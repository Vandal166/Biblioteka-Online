<?php
$response = ['success' => false, 'files' => []];

$folderPath = dirname(__DIR__) . '/books/'; // Ścieżka do folderu z plikami PDF
if (is_dir($folderPath)) 
{
    $files = array_filter(scandir($folderPath), function ($file) use ($folderPath) 
    {
        $filePath = $folderPath . $file;
        // Filtruj tylko pliki z rozszerzeniem .pdf
        return is_file($filePath) && preg_match('/\.pdf$/i', $file);
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
