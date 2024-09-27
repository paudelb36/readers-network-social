<!-- Left Sidebar (Fixed) -->
<div class="w-1/4 fixed top-16 left-0 p-4 space-y-4 bg-gray-100 h-full overflow-y-auto">
            <div class="bg-white p-4 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-2">Daily Book Recommendation</h2>
                <div class="flex items-center space-x-4" id="book-recommendation">
                    <a href="#" id="book-link" target="_blank">
                        <img src="" loading="lazy" class="w-16 h-24 rounded-lg" alt="Book Cover" id="book-cover">
                    </a>
                    <div>
                        <a href="#" id="book-link-title" class="font-medium" target="_blank">
                            <h3 id="book-title"></h3>
                        </a>
                        <p class="text-gray-600 text-sm" id="book-author"></p>
                    </div>
                </div>
            </div>
            <!-- Shortcuts -->
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-xs">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Shortcuts</h2>
                <ul class="space-y-3">
                    <li>
                        <a href="./index.php" class="text-blue-600 hover:underline hover:text-blue-800">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $loggedIn ? './friends.php' : '#'; ?>"
                            class="text-gray-600 hover:text-blue-600 transition-colors duration-200 ease-in-out"
                            onclick="return <?php echo $loggedIn ? 'true' : 'showLoginAlert()'; ?>">
                            Friends
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-blue-600 hover:underline hover:text-blue-800">
                            Trendings
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $loggedIn ? './notification.php' : '#'; ?>"
                            class="relative text-gray-600 hover:text-blue-600 transition-colors duration-200 ease-in-out"
                            onclick="return <?php echo $loggedIn ? 'true' : 'showLoginAlert()'; ?>">
                            Notifications
                            <?php if ($loggedIn && $unreadCount > 0): ?>
                                <span class="absolute top-0 right-0 inline-block w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center transform translate-x-2 -translate-y-2">
                                    <?= $unreadCount; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php if ($loggedIn): ?>
                        <li>
                            <a href="profile.php?user_id=<?php echo $_SESSION['user_id']; ?>" class="text-blue-600 hover:underline hover:text-blue-800">
                                Profile
                            </a>
                        </li>
                    <?php else: ?>
                        <li>
                            <a href="login.php" class="text-blue-600 hover:underline hover:text-blue-800">
                                Login
                            </a>
                        </li>
                        <li>
                            <a href="register.php" class="text-blue-600 hover:underline hover:text-blue-800">
                                Sign Up
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

        </div>