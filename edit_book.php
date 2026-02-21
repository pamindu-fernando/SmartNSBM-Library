<?php
// edit_book.php
include 'db_config.php';
header('Content-Type: application/json');

// Get the raw POST data (JSON)
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id'])) {
    $id = (int)$data['id'];
    $title = mysqli_real_escape_string($conn, $data['title']);
    $author = mysqli_real_escape_string($conn, $data['author']);
    $category = mysqli_real_escape_string($conn, $data['category']);
    // NEW: Capture description
    $description = mysqli_real_escape_string($conn, $data['description'] ?? '');

    // Update query with description
    $sql = "UPDATE books SET title='$title', author='$author', category='$category', description='$description' WHERE id=$id";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing ID']);
}
?>