function logout() {
  if (confirm("Are you sure you want to log out?")) {
    fetch("logout.php").then(() => (window.location.href = "login.php"));
  }
}
document.addEventListener("DOMContentLoaded", function () {
  const ratingFilter = document.getElementById("rating-filter");
  const reviewsList = document.getElementById("reviews-list");
  const applyFilterBtn = document.getElementById("apply-filter-btn");

  function filterReviews() {
    const selectedRating = ratingFilter.value;

    const reviewCards = reviewsList.querySelectorAll(".review-card");
    reviewCards.forEach((card) => {
      const rating = card.getAttribute("data-rating");

      const matchesRating =
        selectedRating === "all" ||
        parseInt(rating) >= parseInt(selectedRating);

      if (matchesRating) {
        card.style.display = "block";
      } else {
        card.style.display = "none";
      }
    });
  }

  applyFilterBtn.addEventListener("click", filterReviews);
});
function filterTable() {
  const searchInput = document.getElementById("table-search");
  const filter = searchInput.value.toLowerCase();
  const tableBody = document.getElementById("booking-table-body");
  const rows = tableBody.getElementsByTagName("tr");

  for (let i = 0; i < rows.length; i++) {
    const userNameCell = rows[i].getElementsByTagName("td")[4];
    if (userNameCell) {
      const userName = userNameCell.textContent || userNameCell.innerText;
      if (userName.toLowerCase().indexOf(filter) > -1) {
        rows[i].style.display = "";
      } else {
        rows[i].style.display = "none";
      }
    }
  }
}

let currentSortColumn = -1;
let sortDirection = 1;

function sortTable(columnIndex) {
  const table = document.querySelector(".chalets-table");
  const tbody = document.getElementById("booking-table-body");
  const rows = Array.from(tbody.querySelectorAll("tr"));

  document.querySelectorAll(".sort-icon").forEach((icon) => {
    icon.classList.remove("active", "asc", "desc");
  });

  if (currentSortColumn === columnIndex) {
    sortDirection *= -1;
  } else {
    currentSortColumn = columnIndex;
    sortDirection = 1;
  }

  const sortIcon = document.getElementById(`sort-icon-${columnIndex}`);
  sortIcon.classList.add("active");
  sortIcon.classList.add(sortDirection === 1 ? "asc" : "desc");

  rows.sort((a, b) => {
    let aValue, bValue;

    const aCell = a.cells[columnIndex];
    const bCell = b.cells[columnIndex];

    aValue = aCell.getAttribute("data-sort-value") || aCell.textContent;
    bValue = bCell.getAttribute("data-sort-value") || bCell.textContent;

    if (columnIndex === 0 || columnIndex === 5) {
      aValue = isNaN(aValue) ? aValue : Number(aValue);
      bValue = isNaN(bValue) ? bValue : Number(bValue);
    }

    if (aValue < bValue) return -1 * sortDirection;
    if (aValue > bValue) return 1 * sortDirection;
    return 0;
  });

  while (tbody.firstChild) {
    tbody.removeChild(tbody.firstChild);
  }

  rows.forEach((row) => tbody.appendChild(row));
}
