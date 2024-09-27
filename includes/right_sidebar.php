 <!-- Right Sidebar (Fixed) -->
 <div class="w-1/4 fixed top-16 right-0 p-4 space-y-4 bg-gray-100 h-full overflow-y-auto">
        <!-- New Friend Suggestions -->

        <div class="bg-white p-4 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-2">Friend Suggestions</h2>
            <ul class="space-y-4" id="suggestions-list">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php
                    // User is logged in, include the friend suggestions logic
                    include 'friend_suggestions.php'; // This should set $suggestedUsers
                    $maxDisplay = 5; // Set the number of initial suggestions to show
                    if (!empty($suggestedUsers)): ?>
                        <?php foreach ($suggestedUsers as $index => $user): ?>
                            <li class="flex items-center justify-between <?php echo $index >= $maxDisplay ? 'hidden' : ''; ?>" id="suggestion-<?php echo $index; ?>">
                                <div class="flex items-center space-x-4">
                                    <a href="profile.php?user_id=<?php echo htmlspecialchars($user['UserID']); ?>" class="flex items-center space-x-4">
                                        <img src="../uploads/profile-pictures/<?php echo htmlspecialchars($user['ProfilePicture']); ?>" class="w-10 h-10 rounded-full" alt="<?php echo htmlspecialchars($user['Username']); ?>">
                                        <div>
                                            <span class="text-gray-800"><?php echo htmlspecialchars($user['FirstName']) . ' ' . htmlspecialchars($user['LastName']); ?></span>
                                            <br>
                                            <span class="block text-sm text-gray-500">@<?php echo htmlspecialchars($user['Username']); ?></span>
                                        </div>
                                    </a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                        <!-- Show More button -->
                        <?php if (count($suggestedUsers) > $maxDisplay): ?>

                            <div class="text-center mt-4">
                                <a href="friends.php" class="inline-block bg-blue-500 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-600 transition duration-300 ease-in-out">
                                    Show More
                                </a>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <p>No suggestions available at the moment.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-gray-600">
                        Please <a href="login.php" class="text-blue-500 hover:underline">log in</a> to see friend suggestions.
                    </p>
                <?php endif; ?>
            </ul>
        </div>
    </div>
