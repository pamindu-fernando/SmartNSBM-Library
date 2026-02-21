<?php
session_start();
// Security check: Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

include 'db_config.php';

// Fetch all books from the database for the user view
$result = mysqli_query($conn, "SELECT * FROM books ORDER BY id DESC");
$db_books = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['id'] = (int)$row['id'];

    $sizeInBytes = 0;
    if (!empty($row['file_path']) && file_exists($row['file_path'])) {
        $sizeInBytes = filesize($row['file_path']);
    }
    $row['size_bytes'] = $sizeInBytes;

    $db_books[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Library - Browse Books</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-gray-50/50 min-h-screen text-slate-950" x-data="dashboard()">

    <header class="sticky top-0 z-50 border-b bg-green-600/80 backdrop-blur-md text-white shadow-sm">
        <div class="container mx-auto flex h-16 items-center justify-between px-4">
            <div class="flex items-center gap-3">
                <img src="logo.png" alt="Library Logo" class="h-10 w-auto rounded-md object-contain brightness-0 invert">
                <div>
                    <h1 class="text-lg font-semibold leading-tight">Student Library</h1>
                    <p class="text-xs text-green-100">Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Student'); ?></p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <span class="bg-white text-green-700 px-2 py-0.5 rounded-md text-xs font-medium uppercase tracking-wider">Student</span>

                <button @click="logout" class="p-2 hover:bg-green-500 rounded-md text-white transition-colors" title="Logout">
                    <i data-lucide="log-out" class="size-5"></i>
                </button>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-6">
        <div class="mb-6 grid gap-4 sm:grid-cols-2">
            <div class="rounded-xl border bg-white p-6 shadow-sm">
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Available Resources</h3>
                <div class="text-3xl font-bold mt-1" x-text="books.length"></div>
            </div>
            <div class="rounded-xl border bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between pb-2">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Library Size</h3>
                    <i data-lucide="database" class="size-4 text-gray-400"></i>
                </div>
                <div class="text-3xl font-bold text-green-600 mt-1" x-text="stats.totalMB + ' MB'"></div>
            </div>
        </div>

        <div class="rounded-xl border bg-white shadow-sm overflow-hidden">
            <div class="p-6 border-b flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-xl font-bold text-slate-800">Resources Catalog</h2>
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-gray-400"></i>
                    <input type="text" x-model="searchTerm" placeholder="Search title or category..." 
                           class="pl-10 border border-gray-200 rounded-md px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-green-500 w-full sm:w-80 transition-all">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 border-b text-gray-600 font-semibold">
                        <tr>
                            <th class="px-6 py-4">Book Title</th>
                            <th class="px-6 py-4">Author</th>
                            <th class="px-6 py-4">Category</th>
                            <th class="px-6 py-4 text-center">Size</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="book in filteredBooks" :key="book.id">
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-slate-900" x-text="book.title"></td>
                                <td class="px-6 py-4 text-gray-600" x-text="book.author || '---'"></td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-md border border-gray-200 text-xs bg-gray-50 text-gray-600 capitalize" x-text="book.category || 'General'"></span>
                                </td>
                                <td class="px-6 py-4 text-center text-gray-500" x-text="(book.size_bytes / (1024*1024)).toFixed(2) + ' MB'"></td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <button @click="openDetails(book)" class="text-blue-500 hover:text-blue-700 transition-colors" title="View Details">
                                            <i data-lucide="eye" class="size-5"></i>
                                        </button>

                                        <template x-if="book.file_path">
                                            <a :href="'download.php?file=' + encodeURIComponent(book.file_path)" class="text-green-600 hover:text-green-700 transition-colors" title="Download">
                                                <i data-lucide="download" class="size-5"></i>
                                            </a>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <template x-if="filteredBooks.length === 0">
                    <div class="p-12 text-center text-gray-400">
                        <i data-lucide="search-x" class="size-12 mx-auto mb-4 opacity-20"></i>
                        <p class="text-base">No resources found matching your search.</p>
                    </div>
                </template>
            </div>
        </div>
    </main>

    <div x-show="detailsOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        <div @click.away="detailsOpen = false" class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="bg-green-600 p-4 text-white flex justify-between items-center">
                <h3 class="text-lg font-bold" x-text="selectedBook?.title"></h3>
                <button @click="detailsOpen = false" class="hover:bg-green-700 p-1 rounded transition-colors"><i data-lucide="x" class="size-5"></i></button>
            </div>
            <div class="p-6">
                <div class="flex gap-4 mb-4 text-sm text-gray-500 border-b pb-4">
                    <div class="flex items-center gap-1">
                        <i data-lucide="user" class="size-4"></i>
                        <span x-text="selectedBook?.author || 'Unknown'"></span>
                    </div>
                    <div class="flex items-center gap-1">
                        <i data-lucide="tag" class="size-4"></i>
                        <span x-text="selectedBook?.category || 'General'"></span>
                    </div>
                </div>
                
                <h4 class="text-sm font-semibold text-gray-700 mb-2">Description</h4>
                <div class="text-gray-600 text-sm leading-relaxed max-h-60 overflow-y-auto pr-2">
                    <p x-text="selectedBook?.description || 'No description available for this book.'"></p>
                </div>

                <div class="mt-6 flex justify-end">
                    <button @click="detailsOpen = false" class="px-4 py-2 border rounded-md text-sm text-gray-600 hover:bg-gray-50 mr-2">Close</button>
                    <template x-if="selectedBook?.file_path">
                        <a :href="'download.php?file=' + encodeURIComponent(selectedBook.file_path)" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm flex items-center gap-2 hover:bg-green-700">
                            <i data-lucide="download" class="size-4"></i> Download PDF
                        </a>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <script>
        function dashboard() {
            return {
                searchTerm: '',
                books: <?php echo json_encode($db_books); ?>,
                
                // NEW: State for details modal
                detailsOpen: false,
                selectedBook: null,

                init() {
                    this.$nextTick(() => lucide.createIcons());
                    this.$watch('searchTerm', () => {
                        this.$nextTick(() => lucide.createIcons());
                    });
                },

                get stats() {
                    const totalBytes = this.books.reduce((s, b) => s + (parseInt(b.size_bytes) || 0), 0);
                    return {
                        totalMB: (totalBytes / (1024 * 1024)).toFixed(2)
                    };
                },

                get filteredBooks() {
                    const q = this.searchTerm.toLowerCase();
                    return this.books.filter(b =>
                        b.title.toLowerCase().includes(q) || (b.category && b.category.toLowerCase().includes(q))
                    );
                },

                // NEW: Function to open details modal
                openDetails(book) {
                    this.selectedBook = book;
                    this.detailsOpen = true;
                    this.$nextTick(() => lucide.createIcons());
                },

                logout() {
                    if (confirm("Are you sure you want to log out?")) {
                        window.location.href = 'logout.php';
                    }
                }
            }
        }
    </script>
</body>
</html>