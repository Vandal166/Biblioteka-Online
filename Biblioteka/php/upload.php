<?php
$response = ['success' => false, 'path' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) 
{
    $uploadDir = dirname(__DIR__) . '/images/';
    if (!is_dir($uploadDir)) 
    {
        mkdir($uploadDir, 0777, true);
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
        // Limit wielkości pliku
        $maxFileSize = 2 * 1024 * 1024;
        if ($file['size'] > $maxFileSize) 
        {
            $response['message'] = 'Plik jest zbyt duży. Maksymalny rozmiar to 2 MB.';
        } 
        else 
        {
            // Rozszerzona lista dozwolonych typów MIME
            $allowedTypes = ['image/jpeg', 'image/png', 'image/x-png', 'image/pjpeg', 'application/octet-stream'];

            // Pobranie rzeczywistego typu MIME
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $fileType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            error_log("Typ MIME wykryty: " . $fileType);

            if (!in_array($fileType, $allowedTypes)) 
            {
                $response['message'] = 'Dozwolone są tylko pliki PNG i JPG.';
            } 
            else 
            {
                // Weryfikacja obrazu
                $imageInfo = getimagesize($file['tmp_name']);
                if ($imageInfo === false) 
                {
                    $response['message'] = 'Plik nie jest prawidłowym obrazem.';
                } 
                else 
                {
                    // Generowanie unikalnej nazwy pliku
                    $fileName = uniqid() . '-' . preg_replace('/[^a-zA-Z0-9\.-]/', '_', basename($file['name']));
                    $filePath = $uploadDir . $fileName;

                    if (move_uploaded_file($file['tmp_name'], $filePath)) 
                    {
                        $response['success'] = true;
                        $response['path'] = 'Biblioteka/images/' . $fileName;
                    } 
                    else 
                    {
                        $response['message'] = 'Nie udało się przesłać pliku.';
                    }
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
