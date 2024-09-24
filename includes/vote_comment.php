<?php
require_once '../includes/config.php';

if (isset($_POST['comment_id'], $_POST['vote_value'], $_SESSION['user_id'])) {
    $commentId = $_POST['comment_id'];
    $voteValue = $_POST['vote_value']; // 1 for upvote, -1 for downvote
    $userId = $_SESSION['user_id'];

    // Check if the user already voted on the comment
    $checkQuery = "SELECT * FROM CommentVotes WHERE UserID = :userId AND CommentID = :commentId";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->execute([':userId' => $userId, ':commentId' => $commentId]);

    if ($checkStmt->rowCount() > 0) {
        // Update existing vote
        $updateQuery = "UPDATE CommentVotes SET VoteValue = :voteValue WHERE UserID = :userId AND CommentID = :commentId";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->execute([
            ':voteValue' => $voteValue,
            ':userId' => $userId,
            ':commentId' => $commentId
        ]);
    } else {
        // Insert new vote
        $insertQuery = "INSERT INTO CommentVotes (CommentID, UserID, VoteValue, CreatedAt) 
                        VALUES (:commentId, :userId, :voteValue, NOW())";
        $insertStmt = $pdo->prepare($insertQuery);
        $insertStmt->execute([
            ':commentId' => $commentId,
            ':userId' => $userId,
            ':voteValue' => $voteValue
        ]);
    }

    // Calculate the updated vote count
    $voteCountQuery = "SELECT SUM(VoteValue) as VoteCount FROM CommentVotes WHERE CommentID = :commentId";
    $voteCountStmt = $pdo->prepare($voteCountQuery);
    $voteCountStmt->execute([':commentId' => $commentId]);
    $voteCount = $voteCountStmt->fetchColumn();

    echo json_encode(['success' => true, 'voteCount' => $voteCount]);
}

