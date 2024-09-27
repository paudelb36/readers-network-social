// comment.js
document.querySelectorAll('.comment-button').forEach(button => {
    button.addEventListener('click', function() {
        const reviewId = this.dataset.reviewId;
        const commentSection = document.getElementById(`comment-section-${reviewId}`);
        commentSection.classList.toggle('hidden');
        if (!commentSection.classList.contains('hidden')) {
            loadComments(reviewId);
        }
    });
});

document.querySelectorAll('.comment-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const reviewId = this.dataset.reviewId;
        const commentInput = this.querySelector('.comment-input');
        const comment = commentInput.value.trim();

        if (comment) {
            submitComment(reviewId, comment);
            commentInput.value = '';
        }
    });
});

function loadComments(reviewId) {
    const commentsList = document.querySelector(`#comment-section-${reviewId} .comments-list`);

    fetch(`../includes/get_comments.php?review_id=${reviewId}`)
        .then(response => response.json())
        .then(data => {
            commentsList.innerHTML = '';
            data.comments.forEach(comment => {
                const commentElement = document.createElement('div');
                commentElement.className = 'comment mb-2';
                commentElement.innerHTML = `
                <strong>${comment.Username}:</strong> ${comment.Content}
                <small class="text-gray-500">${comment.CreatedAt}</small>
            `;
                commentsList.appendChild(commentElement);
            });
        });
}

function submitComment(reviewId, comment) {
    fetch('../includes/add_comment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `review_id=${reviewId}&comment=${encodeURIComponent(comment)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadComments(reviewId);
                const commentCount = document.querySelector(`[data-review-id="${reviewId}"] .comment-count`);
                commentCount.textContent = parseInt(commentCount.textContent) + 1;
            }
        });
}