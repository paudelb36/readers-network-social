<!-- components/sidebar.php -->
<aside class="w-64 bg-gray-700 text-white h-screen">
    <div class="p-6">
        <h2 class="text-lg font-semibold">Menu</h2>
        <nav class="mt-6">
            <ul>
                <li class="mb-4">
                    <a href="index.php" class="block py-2 px-4 hover:bg-gray-600 rounded">Dashboard</a>
                </li>
                <li class="mb-4">
                    <a href="users_info.php" class="block py-2 px-4 hover:bg-gray-600 rounded">Users</a>
                </li>
                <li class="mb-4">
                    <button class="w-full text-left py-2 px-4 hover:bg-gray-600 rounded focus:outline-none" onclick="toggleDropdown('reportsDropdown')">Reports</button>
                    <ul id="reportsDropdown" class="hidden ml-4 mt-2 space-y-2">
                        <li>
                            <a href="reports.php" class="block py-2 px-4 hover:bg-gray-600 rounded">User Reports</a>
                        </li>
                        <li>
                        <a href="content_filtering.php" class="block py-2 px-4 hover:bg-gray-600 rounded">Content Filtering</a>
                        </li>
                    </ul>
                </li>
                
                <li class="mb-4">
                    <a href="settings.php" class="block py-2 px-4 hover:bg-gray-600 rounded">Settings</a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
<script>
function toggleDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    dropdown.classList.toggle('hidden');
}
</script>
