<?php
// feed/like_comment_scripts.php
?>
<script src="../assets/js/report.js"></script>
<script src="../assets/js/comment.js"></script>
<script src="../assets/js/options_menu.js"></script>
<script>
    // Like functionality
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

                        // Toggle heart icon to full red when liked
                        const heartIcon = this.querySelector('.heart-icon');
                        if (this.classList.contains('liked')) {
                            heartIcon.setAttribute('fill', 'red'); // Make the heart fully red
                            heartIcon.setAttribute('stroke', 'red'); // Change the stroke color to red
                        } else {
                            heartIcon.setAttribute('fill', 'none'); // Reset to outline
                            heartIcon.setAttribute('stroke', 'currentColor'); // Reset to original stroke color
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });
</script>