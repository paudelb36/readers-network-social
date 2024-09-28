<?php
// feed/index.php

require_once '../includes/config.php';
require_once 'feed_logic.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !$_SESSION['logged_in']) {
    header('Location: ../login.php');
    exit();
}

// Get reviews data
$reviews = getReviews($pdo);
$likes = getLikes($pdo);
$comments = getComments($pdo);
$userLikes = getUserLikes($pdo, $_SESSION['user_id']);

// Include the header
include_once '../includes/header.php';
?>

<div class="container mx-auto px-4">
    <?php
    include 'report_modal.php';
    include 'display_reviews.php';
    ?>
</div>

<?php
include 'like_comment_scripts.php';
?>