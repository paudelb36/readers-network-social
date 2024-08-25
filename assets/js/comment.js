document.querySelectorAll('.comment-form').forEach(form => {
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        console.log('Form submission intercepted'); // Log message

        const postId = this.querySelector('[name="post_id"]').value || null;
        const reviewId = this.querySelector('[name="review_id"]').value || null;
        const commentText = this.querySelector('[name="comment_text"]').value.trim();

        if (!commentText) {
            alert("Comment cannot be empty.");
            return;
        }

        const formData = new URLSearchParams({
            'post_id': postId,
            'review_id': reviewId,
            'comment_text': commentText
        });

        fetch('comment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: formData
        })
        .then(response => {
            console.log('Response received'); // Log message
            return response.json();
        })
        .then(data => {
            console.log('Data processed:', data); // Log data for debugging

            if (data.success) {
                const commentSection = document.getElementById(`comments-${postId || reviewId}`);
                const newComment = `
                    <div class="media flex pb-4">
                        <a class="mr-4" href="#">
                            <img class="rounded-full max-w-none w-12 h-12" src="${data.profilePicture}" alt="Comment Profile Picture" />
                        </a>
                        <div class="media-body">
                            <div>
                                <a class="inline-block text-base font-bold mr-2" href="profile.php?user_id=${data.userId}">${data.username}</a>
                                <span class="text-slate-500 dark:text-slate-300">${data.createdAt}</span>
                            </div>
                            <p>${commentText}</p>
                            <div class="mt-2 flex items-center">
                                <a class="inline-flex items-center py-2 mr-3 edit-comment-button" data-comment-id="${data.commentId}" href="#">Edit</a>
                                <a class="inline-flex items-center py-2 text-red-500 hover:text-red-700 delete-comment-button" data-comment-id="${data.commentId}" href="#">Delete</a>
                            </div>
                        </div>
                    </div>
                `;
                commentSection.querySelector('.comments-list').insertAdjacentHTML('beforeend', newComment);
                this.querySelector('[name="comment_text"]').value = '';
            } else {
                alert("Failed to add comment. Please try again.");
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("An error occurred. Please try again.");
        });
    });
});
