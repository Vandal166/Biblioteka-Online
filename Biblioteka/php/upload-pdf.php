<?php
$response = ['success' => false, 'path' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) 
{
    $uploadDir = dirname(__DIR__) . '/books/'; // Folder docelowy dla plików PDF
    if (!is_dir($uploadDir)) 
    {
        mkdir($uploadDir, 0777, true); // Tworzenie folderu, jeśli nie istnieje
    }

    $file = $_FILES['file'];

    // Logowanie szczegółów pliku
    error_log("Nazwa pliku: " . $file['name']);
    error_log("Rozmiar pliku: " . $file['size']);
    error_log("Kod błędu przesyłania: " . $file['error']);

    if ($file['error'] !== UPLOAD_ERR_OK) 
    {
        switch ($file['error']) 
        {
            case UPLOAD_ERR_INI_SIZE:
                $response['message'] = 'Plik przekracza limit upload_max_filesize.';
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $response['message'] = 'Plik przekracza maksymalny rozmiar określony w formularzu.';
                break;
            case UPLOAD_ERR_PARTIAL:
                $response['message'] = 'Plik został przesłany tylko częściowo.';
                break;
            case UPLOAD_ERR_NO_FILE:
                $response['message'] = 'Nie przesłano pliku.';
                break;
            default:
                $response['message'] = 'Nieznany błąd przesyłania.';
        }
    } 
    else 
    {
        // Limit wielkości pliku (np. 5 MB)
        $maxFileSize = 5 * 1024 * 1024;
        if ($file['size'] > $maxFileSize) 
        {
            $response['message'] = 'Plik jest zbyt duży. Maksymalny rozmiar to 5 MB.';
        } 
        else 
        {
            // Typ MIME dla plików PDF
            $allowedTypes = ['application/pdf'];

            // Pobranie rzeczywistego typu MIME
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $fileType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            error_log("Typ MIME wykryty: " . $fileType);

            if (!in_array($fileType, $allowedTypes)) 
            {
                $response['message'] = 'Dozwolone są tylko pliki PDF.';
            } 
            else 
            {
                // Generowanie unikalnej nazwy pliku
                $fileName = uniqid() . '-' . preg_replace('/[^a-zA-Z0-9\.-]/', '_', basename($file['name']));
                $filePath = $uploadDir . $fileName;

                if (move_uploaded_file($file['tmp_name'], $filePath)) 
                {
                    $response['success'] = true;
                    $response['path'] = 'Biblioteka/books/' . $fileName; // Ścieżka relatywna do folderu "books"
                } 
                else 
                {
                    $response['message'] = 'Nie udało się przesłać pliku.';
                }
            }
        }
    }
} 
else 
{
    $response['message'] = 'Nie przesłano pliku.';
}

header('Content-Type: application/json');
echo json_encode($response);
