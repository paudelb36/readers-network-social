<!-- Card -->
<article class="mb-4 break-inside p-6 rounded-xl bg-white dark:bg-slate-800 flex flex-col bg-clip-border shadow-md">
        <div class="flex pb-4 items-center justify-between">
            <!-- Profile Image and User Info -->
            <div class="flex flex-grow">
                <a class="inline-block mr-4" href="#">
                    <img class="rounded-full max-w-none w-12 h-12" src="../uploads/profile-pictures/<?php echo htmlspecialchars($review['ProfilePicture']); ?>" alt="Profile Picture" />
                </a>
                <div class="flex flex-col flex-grow">
                    <div class="flex items-center">
                        <div class="flex-grow">
                            <a class="block text-lg font-bold dark:text-white" href="profile.php?user_id=<?php echo htmlspecialchars($review['UserID']); ?>">
                                <?php echo htmlspecialchars($review['FirstName'] . ' ' . $review['LastName']); ?>
                            </a>
                            <span class="block text-sm text-gray-500 dark:text-gray-400">
                                @<?php echo htmlspecialchars($review['Username']); ?>
                            </span>
                        </div>
                        <!-- Three-Dot Button -->
                        <div class="relative ml-4">
                            <button class="text-gray-500 hover:text-gray-700 focus:outline-none" id="options-button-<?php echo htmlspecialchars($review['ReviewID']); ?>">
                                <svg class="w-6 h-6" viewBox="0 0 32 32">
                                    <path d="M16,10c1.7,0,3-1.3,3-3s-1.3-3-3-3s-3,1.3-3,3S14.3,10,16,10z"></path>
                                    <path d="M16,13c-1.7,0-3,1.3-3,3s1.3,3,3,3s3-1.3,3-3S17.7,13,16,13z"></path>
                                    <path d="M16,22c-1.7,0-3,1.3-3,3s1.3,3,3,3s3-1.3,3-3S17.7,22,16,22z"></path>
                                </svg>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 border border-gray-300 dark:border-gray-700 rounded-md shadow-lg hidden" id="options-menu-<?php echo htmlspecialchars($review['ReviewID']); ?>">
                                <ul class="py-1 text-sm">
                                    <li><a href="view_review.php?review_id=<?php echo htmlspecialchars($review['ReviewID']); ?>" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">View Details</a></li>
                                    <li><a href="#" onclick="openReportModal('post', <?php echo htmlspecialchars($review['ReviewID']); ?>)" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">Report Content</a></li>
                                    <li><a href="#" onclick="openReportModal('user', <?php echo htmlspecialchars($review['UserID']); ?>)" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">Report User</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="text-slate-300 dark:text-slate-200 mt-1">
                        <?php
                        $createdAt = new DateTime($review['CreatedAt']);
                        echo htmlspecialchars($createdAt->format('F j, Y'));
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Report Modal -->
        <div id="report-modal-container" class="fixed inset-0 hidden z-50">
            <!-- Background Overlay -->
            <div class="fixed inset-0 bg-black opacity-50"></div>

            <!-- Modal Content -->
            <div class="flex items-center justify-center h-full">
                <div class="bg-white dark:bg-slate-800 p-6 rounded-lg shadow-lg w-96 relative z-10">
                    <h3 class="text-lg font-semibold mb-4">Report <span id="report-type"></span></h3>
                    <form id="report-form" action="" method="POST">
                        <input type="hidden" name="reported_id" id="reported-id">
                        <input type="hidden" name="report_type" id="report-type-input">

                        <div class="mb-4">
                            <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reason for reporting</label>
                            <select name="reason" id="reason" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 rounded-md shadow-sm focus:outline-none" onchange="toggleOtherReason()">
                                <option value="Spam">Spam</option>
                                <option value="Hate Speech">Hate Speech</option>
                                <option value="Harassment">Harassment</option>
                                <option value="Inappropriate Content">Inappropriate Content</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <!-- Other reason input -->
                        <div id="other-reason-container" class="mb-4 hidden">
                            <label for="other-reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Please specify your reason</label>
                            <input type="text" name="other_reason" id="other-reason" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 rounded-md shadow-sm focus:outline-none" placeholder="Enter your reason here">
                        </div>

                        <div class="flex justify-end">
                            <button type="button" onclick="closeReportModal()" class="px-4 py-2 bg-gray-200 dark:bg-slate-600 rounded-md">Cancel</button>
                            <button type="submit" class="ml-2 px-4 py-2 bg-red-500 text-white rounded-md">Report</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <script src="../assets/js/report.js"></script>

        <!-- Display review details -->
        <div class="mb-4">


            <!-- Flexbox for Image and Details -->
            <div class="flex flex-col md:flex-row items-start md:items-center">
                <!-- Book Cover on the left side -->
                <?php if ($review['Image']): ?>
                    <div class="md:mr-8 mb-4 md:mb-0">
                        <img class="w-36 h-auto rounded-lg shadow-sm" src="<?php echo htmlspecialchars($review['Image']); ?>" alt="Book Cover" />
                    </div>
                <?php endif; ?>

                <!-- Book Information on the right side -->
                <div class="flex-grow">
                    <!-- Title and Author at the top -->
                    <h3 class="text-xl font-extrabold dark:text-white mb-2">
                        <?php echo htmlspecialchars($review['Title']); ?>
                    </h3>
                    <p class="text-md font-semibold dark:text-slate-200 mb-2">
                        by <?php echo htmlspecialchars($review['Author']); ?>
                    </p>
                    <!-- Genre and Rating -->
                    <p class="text-sm font-semibold dark:text-slate-200 mb-2">
                        Genres: <?php echo htmlspecialchars($review['Genre']); ?>
                    </p>


                    <!-- Review Text -->
                    <p class="dark:text-slate-200 mb-4 leading-relaxed">
                        <?php echo nl2br(htmlspecialchars($review['ReviewText'])); ?>
                    </p>

                    <!-- Book Description, if available -->
                    <?php if ($review['Description']): ?>
                        <h3 class="text-md font-semibold dark:text-white mt-4 mb-2">Book Description:</h3>
                        <p class="dark:text-slate-200 mb-4">
                            <?php echo nl2br(htmlspecialchars($review['Description'])); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Like and Comment Section -->
        <div class="flex items-center mt-4">
            <!-- Like Button -->
            <button class="like-button flex items-center mr-4" data-review-id="<?php echo htmlspecialchars($review['ReviewID']); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1 heart-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <span class="like-count"><?php echo $likeCount; ?></span>
            </button>



            <!-- Comment Button -->
            <button class="comment-button flex items-center" data-review-id="<?php echo htmlspecialchars($review['ReviewID']); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <span class="comment-count"><?php echo $commentCount; ?></span>
            </button>
        </div>

        <!-- Comment Section (Initially Hidden) -->
        <div class="comment-section mt-4 hidden" id="comment-section-<?php echo htmlspecialchars($review['ReviewID']); ?>">
            <h3 class="text-lg font-semibold mb-2">Comments</h3>
            <div class="comments-list mb-4">
                <!-- Comments will be loaded here dynamically -->
            </div>
            <form class="comment-form flex" data-review-id="<?php echo htmlspecialchars($review['ReviewID']); ?>">
                <input type="text" class="comment-input flex-grow mr-2 p-2 border rounded" name="comment" placeholder="Write a comment...">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Post</button>
            </form>
        </div>
    </article>
