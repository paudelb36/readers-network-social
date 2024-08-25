<!-- components/header.php -->
<header class="bg-gray-600 text-white p-4">
    <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-xl font-bold">Admin Dashboard</h1>
        <div class="relative">
                <form class="flex items-center">
                    <input type="search" placeholder="Search" class="border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit" class="ml-2 px-3 py-1.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Search</button>
                </form>
            </div>
            <!-- Profile or Login Button -->
                <!-- Profile Dropdown -->
                <div class="relative" id="profileDropdown">
                    <button class="flex items-center space-x-2 text-white-900 hover:text-blue-600" id="profileButton">
                        <span>Admin</span>
                        <svg class="profile-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-white shadow-lg rounded-lg hidden" id="dropdownContent">
                        <a href="./profile.php" class="block px-4 py-2 text-gray-600 hover:bg-gray-100">Profile</a>
                        <a href="#" class="block px-4 py-2 text-gray-600 hover:bg-gray-100">Settings</a>
                        <hr class="my-1 border-gray-200">
                        <a href="./logout.php" class="block px-4 py-2 text-gray-600 hover:bg-gray-100">Logout</a>
                    </div>
                </div>
                <!-- Login Button -->
                <!-- <a href="./login.php" class="px-3 py-1.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Login</a> -->
        </div>
</header>
<script>
        // Toggle the profile dropdown
        document.addEventListener('DOMContentLoaded', function () {
            const profileButton = document.getElementById('profileButton');
            const dropdownContent = document.getElementById('dropdownContent');
            const profileDropdown = document.getElementById('profileDropdown');

            profileButton.addEventListener('click', function (event) {
                event.stopPropagation();
                dropdownContent.classList.toggle('hidden');
            });

            // Close the dropdown when clicking outside of it
            document.addEventListener('click', function (event) {
                if (!profileDropdown.contains(event.target)) {
                    dropdownContent.classList.add('hidden');
                }
            });
        });
    </script>