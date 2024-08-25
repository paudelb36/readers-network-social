async function fetchBookRecommendation() {
    const ONE_HOUR_IN_MS = 60 * 60 * 1000; // One hour in milliseconds
  
    // Check if book data is already stored in local storage
    const storedBookData = localStorage.getItem("recommendedBook");
    const storedBookTime = localStorage.getItem("recommendedBookTime");
  
    if (storedBookData && storedBookTime) {
      const currentTime = Date.now();
      const timeDifference = currentTime - storedBookTime;
  
      // Display stored book if it's less than an hour old
      if (timeDifference < ONE_HOUR_IN_MS) {
        const book = JSON.parse(storedBookData);
        populateBookData(book);
        return;
      }
    }
  
    // Fetch new recommendation if data doesn't exist or is older than an hour
    const response = await fetch(
      "https://www.googleapis.com/books/v1/volumes?q=subject:fiction&maxResults=40"
    );
    const data = await response.json();
  
    // Select a random book from the results
    const randomIndex = Math.floor(Math.random() * data.items.length);
    const book = data.items[randomIndex].volumeInfo;
  
    // Populate the HTML with book data and store in local storage
    populateBookData(book);
    localStorage.setItem("recommendedBook", JSON.stringify(book));
    localStorage.setItem("recommendedBookTime", Date.now());
  }
  
  function populateBookData(book) {
    const bookLink = book.infoLink || "#"; // Use the infoLink from the API or a fallback
    document.getElementById("book-cover").src =
      book.imageLinks?.thumbnail || "placeholder.jpg"; // Use a placeholder if no image
    document.getElementById("book-title").innerText =
      book.title || "No Title Available";
    document.getElementById("book-author").innerText = book.authors
      ? `by ${book.authors.join(", ")}`
      : "Unknown Author";
  
    // Set the href for the links
    document.getElementById("book-link").href = bookLink;
    document.getElementById("book-link-title").href = bookLink;
  }
  
  // Call the function to fetch and display the book recommendation
  fetchBookRecommendation();