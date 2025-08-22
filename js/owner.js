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
