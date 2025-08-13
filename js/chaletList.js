const chaletData = [
  {
    id: 1,
    name: "Chalet A",
    location: "Nablus",
    price: 150,
    rating: 4.5,
    description: "A cozy chalet with stunning mountain views.",
    images: [
      "../images/poolpics/pool1.jpg",
      "../images/interiorpics/int1.jpg",
      "../images/bedroompics/bed1.jpg",
    ],
    amenities: ["WiFi", "Parking", "Kitchen"],
    comments: [
      {
        user: "Ahmad",
        rating: 4.5,
        comment: "Amazing stay! Highly recommend.",
        date: "2 days ago",
      },
      {
        user: "Moe",
        rating: 5,
        comment: "Beautiful location and great service.",
        date: "1 week ago",
      },
    ],
  },
  {
    id: 2,
    name: "Chalet B",
    location: "Nablus",
    price: 150,
    rating: 4.5,
    description: "A cozy chalet with stunning mountain views.",
    images: [
      "../images/poolpics/pool2.jpg",
      "../images/interiorpics/int2.jpg",
      "../images/bedroompics/bed2.jpg",
    ],
    amenities: ["WiFi", "Parking", "Kitchen"],
    comments: [
      {
        user: "Ahmad",
        rating: 4,
        comment: "Amazing stay! Highly recommend.",
        date: "4 days ago",
      },
      {
        user: "Moe",
        rating: 5,
        comment: "Beautiful location and great service.",
        date: "6 days ago",
      },
    ],
  },
  {
    id: 3,
    name: "Chalet C",
    location: "Nablus",
    price: 150,
    rating: 4.5,
    description: "A cozy chalet with stunning mountain views.",
    images: [
      "../images/poolpics/pool3.jpg",
      "../images/interiorpics/int3.jpg",
      "../images/bedroompics/bed3.jpg",
    ],
    amenities: ["WiFi", "Parking", "Kitchen"],
    comments: [
      {
        user: "Ahmad",
        rating: 5,
        comment: "Amazing stay! Highly recommend.",
        date: "4 days ago",
      },
      {
        user: "Moe",
        rating: 5,
        comment: "Beautiful location and great service.",
        date: "4 days ago",
      },
    ],
  },
  {
    id: 4,
    name: "Chalet D",
    location: "Nablus",
    price: 150,
    rating: 4.6,
    description: "A cozy chalet with stunning mountain views.",
    images: [
      "../images/poolpics/pool4.jpg",
      "../images/interiorpics/int4.jpg",
      "../images/bedroompics/bed4.jpg",
    ],
    amenities: ["WiFi", "Parking", "Kitchen"],
    comments: [
      {
        user: "Ahmad",
        rating: 5,
        comment: "Amazing stay! Highly recommend.",
        date: "4 days ago",
      },
      {
        user: "Moe",
        rating: 5,
        comment: "Beautiful location and great service.",
        date: "4 days ago",
      },
    ],
  },
];
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

function renderChalet(chaletData) {
  return `
    <div class="chalet" data-id="${chaletData.id}">
    <img src="${chaletData.images[0]}" alt="${
    chaletData.name
  }" class="chalet-image" loading="lazy">
    <div class="chalet-details">
        <h3 class="chalet-name">${chaletData.name}</h3>
        <div class="locationcontainer">
        <img src="../images/pin.png" alt="pin Icon" class="pin-icon">
        <p class="chalet-location">${chaletData.location}</p>
        </div>
        <div class="chalet-footer">
        <div class="rating">
        <div class="stars">
            ${ratingStars(chaletData.rating)}
        </div>
        <span class="rating-value">${chaletData.rating}</span>
        </div>  
        <div class="price">$${chaletData.price} /hour</div>
        </div>
        </div>
        </div>
    `;
}

function detailView(chaletData) {
  const commentsHTML = chaletData.comments
    .map(
      (comment) => `
    <div class="comment">
    <div class="comment-header">
     <span class="comment-user">${comment.user}</span>
     <div class="comment-rating">${ratingStars(comment.rating)}</div>
     <span class="comment-date">${comment.date}</span>
     </div>
     <p class="comment-text">${comment.comment}</p>
    </div>
  `
    )
    .join("");

  const featuresHTML = chaletData.amenities
    .map(
      (feature) => `
        <span class="feature-tag">${feature}</span>
    `
    )
    .join("");

  const rentalOptionsHTML = rentalHours
    .map((hours) => {
      const totalPrice = (chaletData.price * hours).toFixed(2);
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

      return `
            <div class="rental-option" data-hours="${hours}" data-price="${totalPrice}">
                <div class="rental-time">${timeLabel}</div>
                <div class="rental-price">$${totalPrice}</div>
            </div>
        `;
    })
    .join("");

  return `
        <div class="modal-overlay" id="detailModal">
            <div class="modal-content">
                <button class="modal-close" onclick="closeModal()">&times;</button>
                
                <div class="modal-header">
                    <h2>${chaletData.name}</h2>
                    <div class="modal-rating">
                        <div class="stars">
                            ${ratingStars(chaletData.rating)}
                        </div>
                        <span class="rating-text">${chaletData.rating} (${
    chaletData.comments.length
  } reviews)</span>
                    </div>
                </div>

                <div class="modal-body">
                    <div class="modal-images">
                        <div class="main-image">
                            <img src="${chaletData.images[0]}" alt="${
    chaletData.name
  }" id="mainImage">
                        </div>
                        <div class="thumbnail-images">
                            ${chaletData.images
                              .map(
                                (img, index) => `
                                <img src="${img}" alt="${chaletData.name} ${
                                  index + 1
                                }" 
                                     class="thumbnail ${
                                       index === 0 ? "active" : ""
                                     }" 
                                     onclick="changeMainImage('${img}', this)">
                            `
                              )
                              .join("")}
                        </div>
                    </div>

                    <div class="modal-info">
                        <div class="description-section">
                            <h3>Description</h3>
                            <p>${chaletData.description}</p>
                        </div>

                        <div class="features-section">
                            <h3>Features</h3>
                            <div class="features-list">
                                ${featuresHTML}
                            </div>
                        </div>

                        <div class="rental-section">
                            <h3>Rental Options</h3>
                            <div class="rental-options">
                                ${rentalOptionsHTML}
                            </div>
                            <div class="selected-rental">
                                <span>Selected: <span id="selectedTime">12 hours</span></span>
                                <span>Total: <span id="selectedPrice">$${(
                                  chaletData.price * 12
                                ).toFixed(2)}</span></span>
                            </div>
                            <button class="rent-button" onclick="rentItem(${
                              chaletData.id
                            })">Rent Now</button>
                        </div>

                        <div class="comments-section">
                            <h3>Customer Reviews</h3>
                            <div class="comments-list">
                                ${commentsHTML}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function openDetailModal(cardId) {
  const cardData = chaletData.find((item) => item.id == cardId);
  const modalHTML = detailView(cardData);

  const existingModal = document.getElementById("detailModal");
  if (existingModal) {
    existingModal.remove();
  }

  document.body.insertAdjacentHTML("beforeend", modalHTML);
  addRentalOptionListeners();
  document.body.style.overflow = "hidden";
}

function closeModal() {
  const modal = document.getElementById("detailModal");
  if (modal) {
    modal.remove();
    document.body.style.overflow = "auto";
  }
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
  closeModal();
}

function renderCards() {
  const container = document.getElementById("chaletscont");

  container.innerHTML = `
        <div class="loading">
            <div class="spinner"></div>
            Loading Chalets...
        </div>
    `;

  setTimeout(() => {
    const cardsHTML = chaletData.map((card) => renderChalet(card)).join("");
    container.innerHTML = cardsHTML;

    addCardEventListeners();
    //enterance animation
    const cards = document.querySelectorAll(".chalet");
    cards.forEach((card, index) => {
      card.style.opacity = "0";
      card.style.transform = "translateY(20px)";
      setTimeout(() => {
        card.style.transition = "opacity 0.5s ease, transform 0.5s ease";
        card.style.opacity = "1";
        card.style.transform = "translateY(0)";
      }, index * 100);
    });
  }, 1000);
}

function addCardEventListeners() {
  const cards = document.querySelectorAll(".chalet");

  cards.forEach((card) => {
    card.addEventListener("click", () => {
      const cardId = card.getAttribute("data-id");
      openDetailModal(cardId);
    });

    card.addEventListener("mouseenter", () => {
      card.style.cursor = "pointer";
    });
  });
}

document.addEventListener("DOMContentLoaded", () => {
  renderCards();

  document.addEventListener("click", (e) => {
    if (e.target.classList.contains("modal-overlay")) {
      closeModal();
    }
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      closeModal();
    }
  });
});
