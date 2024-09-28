<?php
// feed/like_comment_scripts.php
?>
<script src="../assets/js/report.js"></script>
<script src="../assets/js/comment.js"></script>
<script src="../assets/js/options_menu.js"></script>
<script>
    document.querySelectorAll('.like-button').forEach(button => {
        button.addEventListener('click', function() {
            const reviewId = this.dataset.reviewId;
            const likeCount = this.querySelector('.like-count');
            const heartIcon = this.querySelector('.heart-icon');
            const isLiked = this.classList.contains('liked');

            fetch('../includes/like_review.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `review_id=${reviewId}&action=${isLiked ? 'unlike' : 'like'}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.classList.toggle('liked');
                        likeCount.textContent = data.likeCount;

                        if (this.classList.contains('liked')) {
                            heartIcon.setAttribute('fill', 'red');
                            heartIcon.setAttribute('stroke', 'red');
                        } else {
                            heartIcon.setAttribute('fill', 'none');
                            heartIcon.setAttribute('stroke', 'currentColor');
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });
</script>