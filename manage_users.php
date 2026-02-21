<?php
session_start();
require_once 'db_config.php';

// Security check: Only admins should see this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Handle User Deletion
if (isset($_GET['delete_id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    
    // Prevent admin from deleting themselves
    if ($id == $_SESSION['user_id']) {
        $msg = "You cannot delete your own account.";
    } else {
        $deleteSql = "DELETE FROM users WHERE id = '$id'";
        if (mysqli_query($conn, $deleteSql)) {
            $msg = "User deleted successfully.";
        }
    }
}

// Fetch all users
$users = mysqli_query($conn, "SELECT id, fullname, username, role, created_at FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50 p-8">
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-xl font-bold">Manage Library Users</h2>
            <a href="main.php" class="text-sm text-green-600 font-medium">← Back to Dashboard</a>
        </div>

        <?php if (isset($msg)): ?>
            <div class="p-4 bg-blue-50 text-blue-800 text-sm"><?php echo $msg; ?></div>
        <?php endif; ?>

        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold">
                <tr>
                    <th class="px-6 py-4">Name</th>
                    <th class="px-6 py-4">Username</th>
                    <th class="px-6 py-4">Role</th>
                    <th class="px-6 py-4">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php while($row = mysqli_fetch_assoc($users)): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-sm font-medium"><?php echo $row['fullname']; ?></td>
                    <td class="px-6 py-4 text-sm text-gray-600"><?php echo $row['username']; ?></td>
                    <td class="px-6 py-4 text-sm">
                        <span class="px-2 py-1 rounded-full text-xs <?php echo $row['role'] == 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700'; ?>">
                            <?php echo $row['role']; ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="manage_users.php?delete_id=<?php echo $row['id']; ?>" 
                           onclick="return confirm('Are you sure you want to delete this user?')"
                           class="text-red-600 hover:text-red-800">
                           <i data-lucide="trash-2" class="h-4 w-4"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>