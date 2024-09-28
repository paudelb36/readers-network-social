<?php
// feed/feed_logic.php

function debug_log($message)
{
    error_log("Debug: " . $message);
}

function getReviews($pdo)
{
    $reviewsQuery = "
        SELECT r.ReviewID, r.UserID, r.BookID, r.ReviewText, r.CreatedAt, 
               r.Title, r.Author, r.ISBN, r.PublicationYear, r.Genre, r.Description, r.Image,
               u.Username, u.ProfilePicture, u.FirstName, u.LastName
        FROM Reviews r
        JOIN Users u ON r.UserID = u.UserID
        WHERE r.Status = 'visible'
        ORDER BY r.CreatedAt DESC
    ";
    $stmt = $pdo->prepare($reviewsQuery);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getLikes($pdo)
{
    $likesQuery = "SELECT ReviewID, COUNT(*) as LikeCount FROM Likes GROUP BY ReviewID";
    $likesStmt = $pdo->prepare($likesQuery);
    $likesStmt->execute();
    return $likesStmt->fetchAll(PDO::FETCH_KEY_PAIR);
}

function getComments($pdo)
{
    $commentsQuery = "SELECT ReviewID, COUNT(*) as CommentCount FROM Comments GROUP BY ReviewID";
    $commentsStmt = $pdo->prepare($commentsQuery);
    $commentsStmt->execute();
    return $commentsStmt->fetchAll(PDO::FETCH_KEY_PAIR);
}

function getUserLikes($pdo, $userId) {
    $query = "SELECT ReviewID FROM Likes WHERE UserID = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Handle report submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['report_type'])) {
    handleReportSubmission($pdo, $_SESSION['user_id'], $_POST);
}

function handleReportSubmission($pdo, $userId, $postData)
{
    debug_log("Report submission received. Data: " . print_r($postData, true));

    $reporter_id = $userId;
    $reported_id = $postData['reported_id'] ?? null;
    $report_type = $postData['report_type'] ?? null;
    $reason = $postData['reason'] ?? null;

    debug_log("Reporter ID: $reporter_id");

    // Validate input
    if (empty($reported_id) || empty($report_type) || empty($reason)) {
        $_SESSION['error_message'] = "Invalid report data. Please fill in all required fields.";
        return;
    }

    // Handle custom 'Other' reason
    if ($reason === 'Other') {
        $reason = $postData['other_reason'] ?? 'Unspecified';
    }

    try {
        // Initialize variables to store reported user and post IDs
        $reported_user_id = null;
        $reported_post_id = null;

        // Check if it's a post or user report
        if ($report_type == 'post') {
            $reported_post_id = $reported_id;
            $query = $pdo->prepare("SELECT UserID FROM Reviews WHERE ReviewID = ?");
            $query->execute([$reported_id]);
            $reported_user_id = $query->fetchColumn();

            if (!$reported_user_id) {
                throw new Exception("Invalid post ID provided.");
            }
        } else {
            $reported_user_id = $reported_id;
        }

        // Insert the report into the database
        $stmt = $pdo->prepare("INSERT INTO Reports (ReporterID, ReportedUserID, ReportedPostID, Reason, Status, CreatedAt) VALUES (?, ?, ?, ?, 'Pending', NOW())");
        $stmt->execute([$reporter_id, $reported_user_id, $reported_post_id, $reason]);

        $_SESSION['success_message'] = "You have successfully reported this $report_type.";
        debug_log("Report inserted successfully.");
    } catch (Exception $e) {
        debug_log("Report insertion failed: " . $e->getMessage());
        $_SESSION['error_message'] = "An error occurred while submitting your report. Please try again later.";
    }
}

