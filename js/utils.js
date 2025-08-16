const rentalHours = [12, 24, 36, 48, 60, 72, 84, 96, 108, 120, 168];

function ratingStars(rating) {
  const fullStars = Math.floor(rating);
  const halfStar = rating % 1 >= 0.5 ? 1 : 0;
  let starshtml = "";

  for (let i = 0; i < fullStars; i++) {
    starshtml += '<div class="star filled"></div>';
  }
  if (halfStar) {
    starshtml +=
      '<div class="star filled" style="background: linear-gradient(90deg, #ffd700 50%, #ddd 50%)"></div>';
  }
  for (let i = 0; i < 5 - fullStars - halfStar; i++) {
    starshtml += '<div class="star"></div>';
  }
  return starshtml;
}

function changeMainImage(imageSrc, thumbnail) {
  document.getElementById("mainImage").src = imageSrc;
  document
    .querySelectorAll(".thumbnail")
    .forEach((thumb) => thumb.classList.remove("active"));
  thumbnail.classList.add("active");
}

function addRentalOptionListeners() {
  document.querySelectorAll(".rental-option").forEach((option) => {
    option.addEventListener("click", () => {
      document
        .querySelectorAll(".rental-option")
        .forEach((opt) => opt.classList.remove("active"));
      option.classList.add("active");
      const hours = option.dataset.hours;
      const price = option.dataset.price;
      const days = hours >= 24 ? Math.floor(hours / 24) : 0;
      const remainingHours = hours % 24;

      let timeLabel = "";
      if (days > 0) {
        timeLabel = `${days} day${days > 1 ? "s" : ""}`;
        if (remainingHours > 0) {
          timeLabel += ` ${remainingHours}h`;
        }
      } else {
        timeLabel = `${hours} hours`;
      }

      document.getElementById("selectedTime").textContent = timeLabel;
      document.getElementById("selectedPrice").textContent = `$${price}`;
    });
  });

  const defaultOption = document.querySelector(
    '.rental-option[data-hours="12"]'
  );
  if (defaultOption) {
    defaultOption.click();
  }
}

function rentItem(cardId) {
  const selectedOption = document.querySelector(".rental-option.active");
  if (!selectedOption) {
    alert("Please select a rental duration");
    return;
  }

  const cardData = chaletData.find((item) => item.id == cardId);
  const hours = selectedOption.dataset.hours;
  const price = selectedOption.dataset.price;
  const days = hours >= 24 ? Math.floor(hours / 24) : 0;
  const remainingHours = hours % 24;

  let timeLabel = "";
  if (days > 0) {
    timeLabel = `${days} day${days > 1 ? "s" : ""}`;
    if (remainingHours > 0) {
      timeLabel += ` ${remainingHours}h`;
    }
  } else {
    timeLabel = `${hours} hours`;
  }

  alert(
    `Rental Confirmed!\n\nItem: ${cardData.name}\nDuration: ${timeLabel}\nTotal Cost: $${price}\n\nThank you for your rental!`
  );

  window.history.back();
}
