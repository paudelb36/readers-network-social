//report.js
// Function to open the report modal
function openReportModal(reportType, reportedId) {
    document.getElementById('report-modal-container').classList.remove('hidden');
    document.getElementById('reported-id').value = reportedId; // Set the reported ID

    // Update modal content based on report type
    const reportTypeText = reportType === 'post' ? 'Post' : 'User';
    document.getElementById('report-type').textContent = reportTypeText;
    document.getElementById('report-type-input').value = reportType;

    // Show or hide the "Other" reason input based on the current selection
    const reasonSelect = document.getElementById('reason');
    toggleOtherReason(reasonSelect.value); // Set initial state for "Other" reason input

    // Clear the other reason input
    document.getElementById('other-reason').value = '';

    document.body.style.overflow = 'hidden'; // Disable page scrolling
}

// Function to toggle visibility of "Other" reason input
function toggleOtherReason(selectedValue) {
    const otherReasonContainer = document.getElementById('other-reason-container');
    if (selectedValue === 'Other') {
        otherReasonContainer.classList.remove('hidden');
    } else {
        otherReasonContainer.classList.add('hidden');
    }
}

// Close modal when clicking outside the content
document.getElementById('report-modal-container').addEventListener('click', function (event) {
    if (event.target === this) {
        closeReportModal();
    }
});

// Add event listener to the reason select to handle changes
document.getElementById('reason').addEventListener('change', function () {
    toggleOtherReason(this.value);
});
