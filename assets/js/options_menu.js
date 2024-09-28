// feed/js/options_menu.js

document.addEventListener("DOMContentLoaded", function () {
  // Toggle the visibility of the options menu
  document.querySelectorAll('[id^="options-button-"]').forEach((button) => {
    button.addEventListener("click", function (e) {
      e.stopPropagation(); // Prevent bubbling up to the document click listener
      const postId = this.id.split("-").pop();
      const menu = document.getElementById("options-menu-" + postId);
      const isVisible = menu.classList.contains("visible");

      // Hide all other menus
      document.querySelectorAll('[id^="options-menu-"]').forEach((m) => {
        m.classList.remove("visible");
        m.style.opacity = "0";
        m.style.display = "none";
      });

      if (!isVisible) {
        menu.classList.add("visible");
        menu.style.opacity = "1";
        menu.style.display = "block";
      }
    });
  });

  // Close the menu if clicked outside
  document.addEventListener("click", function (e) {
    if (!e.target.closest('[id^="options-button-"]')) {
      document.querySelectorAll('[id^="options-menu-"]').forEach((m) => {
        m.classList.remove("visible");
        m.style.opacity = "0";
        m.style.display = "none";
      });
    }
  });
  // Function to handle post deletion
  window.deletePost = function (postId) {
    if (confirm("Are you sure you want to delete this post?")) {
      // Send delete request to the server
      fetch("../includes/delete_post.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: "post_id=" + postId,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            // Remove the post from the DOM
            const postElement = document.querySelector(
              `article[data-post-id="${postId}"]`
            );
            if (postElement) {
              postElement.remove();
            }
            alert("Post deleted successfully");
          } else {
            alert("Failed to delete post: " + data.message);
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("An error occurred while deleting the post");
        });
    }
  };
});


// Function to open the update form
function openUpdateForm(reviewId) {
  document.getElementById(`update-modal-${reviewId}`).classList.remove('hidden');
}

// Function to close the update form
function closeUpdateForm(reviewId) {
  document.getElementById(`update-modal-${reviewId}`).classList.add('hidden');
}

// Add event listeners for update forms
document.querySelectorAll('[id^="update-form-"]').forEach(form => {
  form.addEventListener('submit', function(e) {
      e.preventDefault();
      const reviewId = this.querySelector('input[name="review_id"]').value;
      const reviewText = this.querySelector('textarea[name="review_text"]').value;

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
              const reviewElement = document.querySelector(`article[data-review-id="${reviewId}"] .dark:text-slate-200.mb-4.leading-relaxed`);
              if (reviewElement) {
                  reviewElement.textContent = reviewText;
              }
              closeUpdateForm(reviewId);
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
});
