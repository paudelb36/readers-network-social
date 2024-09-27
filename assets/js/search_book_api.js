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
  const bookImage = bookCoverImage.src,
    bookTitle = document.getElementById("book_title").value,
    uploadedImage = bookImageUpload.files[0];

  // Validation: Check if image is provided
  if (!uploadedImage && bookImage === "default-image-url.png") {
    return alert(
      "Please upload an image or select one from the search results."
    );
  }

  try {
    let imagePath;

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
    } else {
      // Use the image URL from Google Books
      const response = await fetch("download_image.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          imageUrl: bookImage,
          title: bookTitle,
        }),
      });
      const result = await response.json();
      if (result.success) {
        imagePath = result.imagePath; // Use the path returned by download script
      } else {
        return alert(result.message || "Failed to download image.");
      }
    }

    // Now submit the review with the image path
    const formData = new FormData(reviewForm);
    formData.append("downloaded_image", imagePath);
    const submitResponse = await fetch("create_review.php", {
      method: "POST",
      body: formData,
    });
    if (submitResponse.ok) {
      // Optionally, notify friends here
      // Notify friends logic goes here
      window.location.href = "index.php";
    } else {
      alert("Failed to submit the review. Please try again.");
    }
  } catch (error) {
    alert(error.message);
  }
}

reviewForm.addEventListener("submit", function (e) {
  e.preventDefault();
  submitReview();
});
