<?php
// Ensure to include this file only once
include_once '../includes/config.php'; 

// Fetch user data
$suggestedUsers = [];
$query = "SELECT UserID, Username, FirstName, LastName, ProfilePicture FROM Users WHERE UserID != :currentUserId";
$stmt = $pdo->prepare($query);
$stmt->execute(['currentUserId' => $_SESSION['user_id']]);
$suggestedUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to calculate cosine similarity
if (!function_exists('calculateCosineSimilarity')) {
    function calculateCosineSimilarity($vec1, $vec2) {
        $dotProduct = 0;
        $magnitude1 = 0;
        $magnitude2 = 0;

        for ($i = 0; $i < count($vec1); $i++) {
            $dotProduct += $vec1[$i] * $vec2[$i];
            $magnitude1 += pow($vec1[$i], 2);
            $magnitude2 += pow($vec2[$i], 2);
        }

        $magnitude1 = sqrt($magnitude1);
        $magnitude2 = sqrt($magnitude2);

        if ($magnitude1 * $magnitude2 == 0) {
            return 0; // Prevent division by zero
        }

        return $dotProduct / ($magnitude1 * $magnitude2);
    }
}

// Create genre vector function
if (!function_exists('createGenreVector')) {
    function createGenreVector($userGenres, $allGenres) {
        $vector = [];
        foreach ($allGenres as $genre) {
            $vector[] = in_array($genre, array_map('trim', explode(',', $userGenres))) ? 1 : 0;
        }
        return $vector;
    }
}

// Fetch all users and their favorite genres
$query = $pdo->query("SELECT UserID, Username, FirstName, LastName, ProfilePicture, FavoriteGenres FROM Users");
$users = $query->fetchAll(PDO::FETCH_ASSOC);

// Get the logged-in user's genres
$loggedInUserID = $_SESSION['user_id']; 
$loggedInUser = array_filter($users, function($user) use ($loggedInUserID) {
    return $user['UserID'] == $loggedInUserID;
});
$loggedInUser = array_values($loggedInUser)[0];

// Extract all unique genres across all users
$allGenres = [];
foreach ($users as $user) {
    $genres = explode(',', $user['FavoriteGenres']);
    $allGenres = array_merge($allGenres, $genres);
}
$allGenres = array_unique(array_map('trim', $allGenres));

// Calculate cosine similarity for each user
$suggestedUsers = [];
$loggedInUserVector = createGenreVector($loggedInUser['FavoriteGenres'], $allGenres);

foreach ($users as $user) {
    if ($user['UserID'] != $loggedInUserID) { // Skip the logged-in user
        $userVector = createGenreVector($user['FavoriteGenres'], $allGenres);
        $similarity = calculateCosineSimilarity($loggedInUserVector, $userVector);
        $user['similarity'] = $similarity;
        $suggestedUsers[] = $user;
    }
}

// Sort users by similarity in descending order
usort($suggestedUsers, function($a, $b) {
    return $b['similarity'] <=> $a['similarity'];
});

// Now you can loop through $suggestedUsers to display the friend suggestions
?>
