function renderCards() {
  const chaletsContainer = document.getElementById("chaletscont");

  if (!chaletsContainer) {
    console.error("Chalets container not found");
    return;
  }

  const cardsHTML = chaletData
    .map(
      (chalet) => `
    <div class="chalet" data-id="${chalet.id}">
    <img src="${chalet.images[0]}" alt="${
        chalet.name
      }" class="chalet-image" loading="lazy">
    <div class="chalet-details">
        <h3 class="chalet-name">${chalet.name}</h3>
        <div class="locationcontainer">
        <img src="../images/pin.png" alt="pin Icon" class="pin-icon">
        <p class="chalet-location">${chalet.location}</p>
        </div>
        <div class="chalet-footer">
        <div class="rating">
        <div class="stars">
            ${ratingStars(chalet.rating)}
        </div>
        <span class="rating-value">${chalet.rating}</span>
        </div>  
        <div class="price">$${chaletchaletData.price} /hour</div>
        </div>
        </div>
        </div>
  `
    )
    .join("");

  chaletsContainer.innerHTML = cardsHTML;
}

document.addEventListener("DOMContentLoaded", () => {
  renderCards();
});
