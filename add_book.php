<?php
// ... (keep include and header)
include 'db_config.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'data' => []];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    // NEW: Capture description
    $description = mysqli_real_escape_string($conn, $_POST['description'] ?? '');
    
    // ... (keep file upload logic exactly as is) ...
    $pdfPath = "";
    $newSize = 0;
    if (isset($_FILES['pdfFile']) && $_FILES['pdfFile']['error'] === 0) {
        // ... (existing upload code) ...
        $fileName = time() . "_" . $_FILES['pdfFile']['name'];
        $target = "uploads/" . $fileName;
        if (move_uploaded_file($_FILES['pdfFile']['tmp_name'], $target)) {
            $pdfPath = $target;
            $newSize = filesize($target);
        }
    }

    // NEW: Update INSERT query to include description
    $sql = "INSERT INTO books (title, author, category, description, quantity, available, file_path) 
            VALUES ('$title', '$author', '$category', '$description', 1, 1, '$pdfPath')";

    // ... (keep the rest of the file exactly as is) ...
    if (mysqli_query($conn, $sql)) {
        $response['success'] = true;
        $response['bookId'] = mysqli_insert_id($conn);
        $response['data'] = [
            'pdfFile' => $pdfPath,
            'size_bytes' => $newSize
        ];
    } else {
        $response['message'] = mysqli_error($conn);
    }
}
echo json_encode($response);
?>