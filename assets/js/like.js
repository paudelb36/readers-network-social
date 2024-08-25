document.addEventListener('DOMContentLoaded', function () {
    const likeButtons = document.querySelectorAll('.like-btn');

    likeButtons.forEach(button => {
        button.addEventListener('click', function () {
            const postId = this.getAttribute('data-post-id');
            const postType = this.getAttribute('data-post-type');
            const isLiked = this.classList.contains('liked');

            // Send AJAX request to like/unlike the post
            fetch('like_post.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    postId: postId,
                    postType: postType,
                    like: !isLiked
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI based on the response
                    const likeCountElement = this.querySelector('.like-count');
                    const currentCount = parseInt(likeCountElement.textContent);
                    likeCountElement.textContent = isLiked ? currentCount - 1 : currentCount + 1;
                    this.classList.toggle('liked', !isLiked);
                } else {
                    console.error('Error liking the post:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
});
