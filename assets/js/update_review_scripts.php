<script>
function openUpdateModal(reviewId) {
    fetch(`../includes/get_review.php?review_id=${reviewId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('update-review-id').value = data.ReviewID;
            document.getElementById('update-review-text').value = data.ReviewText;
            document.getElementById('update-review-modal').classList.remove('hidden');
        })
        .catch(error => console.error('Error fetching review data:', error));
}

function closeUpdateModal() {
    document.getElementById('update-review-modal').classList.add('hidden');
}

document.getElementById('update-review-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const reviewId = document.getElementById('update-review-id').value;
    const reviewText = document.getElementById('update-review-text').value;

    fetch('../includes/update_review.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `review_id=${reviewId}&review_text=${encodeURIComponent(reviewText)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the review text in the DOM
            const reviewElement = document.querySelector(`#review-text-${reviewId}`);
            if (reviewElement) {
                reviewElement.innerHTML = reviewText.replace(/\n/g, '<br>');
            }
            closeUpdateModal();
            alert('Review updated successfully');
        } else {
            alert('Failed to update review: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the review');
    });
});
</script>