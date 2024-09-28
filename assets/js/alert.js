function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.textContent = message;
    alertDiv.className = `fixed left-1/2 transform -translate-x-1/2 p-4 rounded-md ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white z-50 transition-all duration-300 ease-in-out`;

    // Position the alert below the header
    const header = document.querySelector('nav');
    const headerHeight = header ? header.offsetHeight : 0;
    alertDiv.style.top = `${headerHeight + 20}px`;

    document.body.appendChild(alertDiv);

    // Fade in
    setTimeout(() => {
        alertDiv.style.opacity = '1';
    }, 10);

    // Fade out and remove
    setTimeout(() => {
        alertDiv.style.opacity = '0';
        setTimeout(() => alertDiv.remove(), 300);
    }, 3000);
}

function showLoginAlert() {
    showAlert("Please log in to access this feature.", "error");
    return false; // Prevents the default link behavior
}
