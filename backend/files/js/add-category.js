function getCategoryList() {
  // AJAX request to fetch the category list
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4 && xhr.status == 200) {
      // Update the category list
      document.getElementById("categoryList").innerHTML = xhr.responseText;
    }
  };
  xhr.open(
    "GET",
    "../../auth/backend-assets/category/get_categories.php",
    true
  );
  xhr.send();
}

// Initial load of category list
getCategoryList();
