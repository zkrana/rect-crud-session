const messageElement = document.getElementById("error");
const currentURL = window.location.href;

if (messageElement) {
  // Set a timeout to hide the message after 5000 milliseconds (5 seconds)
  setTimeout(function () {
    // Hide the message element
    messageElement.style.display = "none";

    // Check if the success or error parameter is present in the URL
    if (currentURL.includes("?success=") || currentURL.includes("?error=")) {
      // Remove the success or error parameter from the URL
      const newURL = currentURL.split("?")[0];
      history.replaceState({}, document.title, newURL);
    }
  }, 5000);
}

function toggleUserOptions() {
  var options = document.getElementById("userOptions");
  options.style.display = options.style.display === "flex" ? "none" : "flex";
}

function showBlockedIP() {
  // Hide other sections and show the Blocked IP section
  document.getElementById("blockedIPSection").style.display = "block";
  document.getElementById("accessLogsSection").style.display = "none";
}

function showAccessLogs() {
  // Hide other sections and show the Access Logs section
  document.getElementById("blockedIPSection").style.display = "none";
  document.getElementById("accessLogsSection").style.display = "block";
}

// Function to update the currency symbol based on the selected currency
function updateCurrencySymbol() {
  var currencySelect = document.getElementById("currency");
  var currencySymbol = document.getElementById("currencySymbol");
  var selectedCurrency = currencySelect.value;

  // Set the currency symbol based on the selected currency
  if (selectedCurrency === "BDT") {
    currencySymbol.textContent = "৳"; // BDT symbol
  } else if (selectedCurrency === "USD") {
    currencySymbol.textContent = "$"; // USD symbol
  }
  // Add more conditions for other currencies as needed
}

// Attach the updateCurrencySymbol function to the change event of the currency select
document
  .getElementById("currency")
  .addEventListener("change", updateCurrencySymbol);

// Call the function initially to set the default currency symbol
updateCurrencySymbol();
