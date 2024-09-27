// like.js
document.querySelectorAll('.like-button').forEach(button => {
    button.addEventListener('click', function() {
        const reviewId = this.dataset.reviewId;
        fetch('../includes/like_review.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `review_id=${reviewId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Toggle like button UI
                    this.classList.toggle('liked');
                    const likeCount = this.querySelector('.like-count');
                    let currentCount = parseInt(likeCount.textContent);
                    likeCount.textContent = this.classList.contains('liked') ? currentCount + 1 : currentCount - 1;
                }
            })
            .catch(error => console.error('Error:', error));
    });
});