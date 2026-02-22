<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    die("Access denied.");
}


if (isset($_GET['file'])) {
    $file_path = $_GET['file'];
    
    $base_dir = __DIR__; 
    $real_path = realpath($file_path);
    if ($real_path && file_exists($real_path)) {
        if (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($real_path) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($real_path));

        readfile($real_path);
        exit;
    } else {
        echo "Error: File not found or access denied.";
    }
} else {
    echo "Error: No file specified.";
}
?>
