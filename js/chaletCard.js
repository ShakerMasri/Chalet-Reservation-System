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

function renderCards(containerId, dataToRender = chaletData, limit = null) {
  const container = document.getElementById("chaletscont");

  container.innerHTML = `
        <div class="loading">
            <div class="spinner"></div>
            Loading Chalets...
        </div>
    `;

  setTimeout(() => {
    const dataToShow = limit ? dataToRender.slice(0, limit) : dataToRender;
    const cardsHTML = dataToShow.map((card) => renderChalet(card)).join("");
    container.innerHTML = cardsHTML;

    addCardEventListeners();
    //entrance animation
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

      window.location.href = `chaletDetails.html?id=${cardId}`;
    });

    card.addEventListener("mouseenter", () => {
      card.style.cursor = "pointer";
    });
  });
}

document.addEventListener("DOMContentLoaded", renderCards);
