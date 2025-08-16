function getUrlParameter(name) {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get(name);
}

function renderDetailView(chaletData) {
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
                <div class="modal-header">
                    <h1>${chaletData.name}</h1>
                    <div class="rating">
                        <div class="stars">
                            ${ratingStars(chaletData.rating)}
                        </div>
                        <span class="rating-value">${chaletData.rating} (${
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

                    <div class="moadal-info">
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
            `;
}

document.addEventListener("DOMContentLoaded", () => {
  const chaletId = getUrlParameter("id");

  if (!chaletId) {
    document.getElementById("chaletDetails").innerHTML =
      "<p>No chalet ID provided.</p>";
    return;
  }

  const chalet = chaletData.find((item) => item.id == chaletId);

  if (!chalet) {
    document.getElementById("chaletDetails").innerHTML =
      "<p>Chalet not found.</p>";
    return;
  }

  document.getElementById("chaletDetails").innerHTML = renderDetailView(chalet);
  addRentalOptionListeners();

  document.title = `${chalet.name} - Chalet Details`;
});
