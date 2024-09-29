// search_book_api.js
const API_KEY = "AIzaSyAjH7g5XK4YYA5t2GH1rVcd2-PKzGsgp0c",
  searchBookButton = document.getElementById("searchBookButton"),
  bookSearchInput = document.getElementById("bookSearch"),
  resultsList = document.getElementById("resultsList"),
  searchResults = document.getElementById("searchResults"),
  reviewForm = document.getElementById("reviewForm"),
  bookCoverImage = document.getElementById("book_cover_image"),
  bookImageUpload = document.getElementById("book_image_upload");

searchBookButton.addEventListener("click", async () => {
  const query = bookSearchInput.value;
  if (!query)
    return alert("Please enter a book title, author, or ISBN to search.");
  try {
    const response = await fetch(
      `https://www.googleapis.com/books/v1/volumes?q=${encodeURIComponent(
        query
      )}&key=${API_KEY}`
    );
    const data = await response.json();
    if (data.items && data.items.length > 0) {
      resultsList.innerHTML = "";
      data.items.forEach((book) => {
        const listItem = document.createElement("li");
        listItem.classList.add(
          "p-2",
          "cursor-pointer",
          "hover:bg-gray-200",
          "flex",
          "items-center",
          "space-x-2"
        );
        const bookImage = book.volumeInfo.imageLinks
          ? book.volumeInfo.imageLinks.thumbnail
          : "default-image-url.png";
        listItem.innerHTML = `
                        <img src="${bookImage}" alt="Book Cover" class="w-8 h-auto">
                        <div>
                            <strong>${book.volumeInfo.title}</strong><br>
                            <span class="text-sm text-gray-600">${
                              book.volumeInfo.authors
                                ? book.volumeInfo.authors.join(", ")
                                : "Unknown Author"
                            }</span>
                        </div>`;
        listItem.onclick = () => populateForm(book.volumeInfo);
        resultsList.appendChild(listItem);
      });
      searchResults.classList.remove("hidden");
    } else alert("No books found. Please try a different search.");
  } catch (error) {
    console.error("Error fetching book data:", error);
    alert(
      "An error occurred while searching for books. Please try again later."
    );
  }
});

function populateForm(book) {
  document.getElementById("book_title").value = book.title || "";
  document.getElementById("book_author").value = book.authors
    ? book.authors.join(", ")
    : "";
  document.getElementById("book_isbn").value = book.industryIdentifiers
    ? book.industryIdentifiers[0].identifier
    : "";
  document.getElementById("book_year").value = book.publishedDate
    ? book.publishedDate.split("-")[0]
    : "";
  document.getElementById("book_genre").value = book.categories
    ? book.categories.join(", ")
    : "";

  // Set book cover image
  const bookImage = book.imageLinks
    ? book.imageLinks.thumbnail
    : "default-image-url.png";
  bookCoverImage.src = bookImage;
  bookCoverImage.classList.remove("hidden");
  document.getElementById("book_image").value = bookImage;

  // Hide search results
  searchResults.classList.add("hidden");
}

async function submitReview() {
  try {
    const bookImage = bookCoverImage.src,
      bookTitle = document.getElementById("book_title"),
      uploadedImage = bookImageUpload.files[0];

    let imagePath = "";

    // If user has uploaded an image manually
    if (uploadedImage) {
      const formData = new FormData();
      formData.append("uploaded_image", uploadedImage);

      const uploadResponse = await fetch("upload_image.php", {
        method: "POST",
        body: formData,
      });
      const uploadResult = await uploadResponse.json();
      if (uploadResult.success) {
        imagePath = uploadResult.imagePath; // Use the path returned by upload script
      } else {
        return alert(uploadResult.message || "Failed to upload the image.");
      }
    } else if (bookImage && bookImage !== "default-image-url.png") {
      // Use the image URL from Google Books
      const response = await fetch("download_image.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          imageUrl: bookImage,
          title: bookTitle ? bookTitle.value : "",
        }),
      });
      const result = await response.json();
      if (result.success) {
        imagePath = result.imagePath; // Use the path returned by download script
      } else {
        return alert(result.message || "Failed to download image.");
      }
    }

    // Function to safely get element value
    const getElementValue = (id) => {
      const element = document.getElementById(id);
      if (element) {
        return element.value.trim();
      } else {
        console.error(`Element with id '${id}' not found`);
        return "";
      }
    };

    // Now submit the review with the image path
    const reviewData = {
      review_text: getElementValue("review_text"), // Ensure this gets the textarea value
      book_title: getElementValue("book_title"),
      book_author: getElementValue("book_author"),
      book_isbn: getElementValue("book_isbn"),
      book_year: getElementValue("book_year"),
      book_genre: getElementValue("book_genre"),
      downloaded_image: imagePath,
    };

    console.log("Review data to be submitted:", reviewData);

    // Check if review text is filled
    if (!reviewData.review_text) {
      console.error("Review text is empty");
      return alert("Please enter your review text.");
    }

    // Check if book title and author are filled
    if (!reviewData.book_title || !reviewData.book_author) {
      console.error("Book title or author is missing");
      return alert("Please ensure book title and author are provided.");
    }

    const submitResponse = await fetch("create_review.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(reviewData),
    });

    console.log("Response status:", submitResponse.status);
    const result = await submitResponse.json();
    console.log("Server response:", result);

    if (result.success) {
      window.location.href = "index.php"; // Redirect on success
    } else {
      console.error("Server reported an error:", result.message);
      alert(result.message || "Failed to submit the review. Please try again.");
    }
  } catch (error) {
    console.error("Error in submitReview function:", error);
    alert(
      "An error occurred while submitting the review. Please check the console for more details."
    );
  }
}

reviewForm.addEventListener("submit", function (e) {
  e.preventDefault(); // Prevent default form submission
  submitReview(); // Call your custom submit function
});
