<?php
session_start();

// 1. Security Check: Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Access denied.");
}

// 2. Get the filename from the URL
if (isset($_GET['file'])) {
    $file_path = $_GET['file'];

    // 3. Security: Prevent "Directory Traversal" attacks (trying to read ../../config.php)
    // Make sure we only download files from the 'uploads' folder (adjust this folder name if yours is different)
    $base_dir = __DIR__; // Or specific folder like __DIR__ . '/uploads/';
    
    // Resolve the real path
    $real_path = realpath($file_path);

    // Check if file exists and is within the allowed directory
    if ($real_path && file_exists($real_path)) {
        
        // 4. Clear any previous output (Crucial for PDF corruption issues)
        if (ob_get_level()) {
            ob_end_clean();
        }

        // 5. Set Headers to force download
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($real_path) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($real_path));

        // 6. Read the file
        readfile($real_path);
        exit;
    } else {
        echo "Error: File not found or access denied.";
    }
} else {
    echo "Error: No file specified.";
}
?>