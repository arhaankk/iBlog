// DOM Elements
const searchForm = document.getElementById("searchForm");
const titleSearch = document.getElementById("titleSearch");
const contentSearch = document.getElementById("contentSearch");
const dateFilter = document.getElementById("dateFilter");
const hasImage = document.getElementById("hasImage");
const userIdFilter = document.getElementById("userIdFilter");

// Function to restore form state from URL query parameters
function restoreFormStateFromUrl() {
    // Parse the URL query parameters
    const urlParams = new URLSearchParams(window.location.search);

    // Restore each form field based on the query parameters
    if (urlParams.has("title")) {
        titleSearch.value = urlParams.get("title");
    }

    if (urlParams.has("content")) {
        contentSearch.value = urlParams.get("content");
    }

    if (urlParams.has("date")) {
        dateFilter.value = urlParams.get("date");
    }

    if (urlParams.has("hasImage")) {
        hasImage.checked = urlParams.get("hasImage") === "true";
    }

    if (urlParams.has("userId")) {
        userIdFilter.value = urlParams.get("userId");
    }
}

function isFormEmpty() {
    return (
        !titleSearch.value.trim() &&
        !contentSearch.value.trim() &&
        !dateFilter.value &&
        !hasImage.checked &&
        !userIdFilter.value.trim()
    );
}

// Event Listener for Form Submission
searchForm.addEventListener("submit", async (e) => {
    e.preventDefault(); // Prevent default form submission

    // Check if the form is empty
    if (isFormEmpty()) {
        alert("Please provide at least one filter criteria.");
        return; // Stop further execution
    }

    // Get Filter Values
    const titleSearchValue = titleSearch.value.trim();
    const contentSearchValue = contentSearch.value.trim();
    const dateFilterValue = dateFilter.value;
    const hasImageValue = hasImage.checked;
    const userIdFilterValue = userIdFilter.value.trim();

    // Build Query Parameters
    const queryParams = new URLSearchParams();
    if (titleSearchValue) queryParams.append("title", titleSearchValue);
    if (contentSearchValue) queryParams.append("content", contentSearchValue);
    if (dateFilterValue) queryParams.append("date", dateFilterValue);
    if (hasImageValue) queryParams.append("hasImage", "true");
    if (userIdFilterValue) queryParams.append("userId", userIdFilterValue);

    // Update the URL with the query parameters
    const newUrl = `?${queryParams.toString()}`;
    window.history.pushState({}, "", newUrl);

    // Reload the page to reflect the updated filters
    location.href = newUrl;
});

// Restore form state when the page loads
document.addEventListener("DOMContentLoaded", () => {
    restoreFormStateFromUrl();
});