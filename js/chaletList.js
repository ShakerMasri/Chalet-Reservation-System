async function fetchChaletDetails(chaletId) {
  try {
    const response = await fetch(`getChaletDetails.php?id=${chaletId}`);
    if (!response.ok) {
      throw new Error("Network response was not ok");
    }
    return await response.json();
  } catch (error) {
    console.error("Error fetching chalet details:", error);
    return null;
  }
}

async function fetchChaletReviews(chaletId) {
  try {
    const response = await fetch(`getChaletReviews.php?id=${chaletId}`);
    if (!response.ok) {
      throw new Error("Network response was not ok");
    }
    return await response.json();
  } catch (error) {
    console.error("Error fetching chalet reviews:", error);
    return [];
  }
}

async function fetchChaletImages(chaletId) {
  try {
    const response = await fetch(`getChaletImages.php?id=${chaletId}`);
    if (!response.ok) {
      throw new Error("Network response was not ok");
    }
    const images = await response.json();

    return images.map((img) => {
      if (img.image_path && strpos(img.image_path, "golden/") === -1) {
        return {
          ...img,
          image_path:
            "../images/golden/" +
            (img.image_path.includes("/")
              ? basename(img.image_path)
              : img.image_path),
        };
      }
      return img;
    });
  } catch (error) {
    console.error("Error fetching chalet images:", error);
    return [];
  }
}

function basename(path) {
  return path.split("/").pop();
}

function strpos(haystack, needle) {
  return haystack.indexOf(needle) !== -1;
}
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
  const imageSrc = chaletData.primary_image || "../images/Home.jpg";

  return `
        <div class="chalet" data-id="${chaletData.chaletId}">
            <img src="${imageSrc}" alt="${
    chaletData.name
  }" class="chalet-image" loading="lazy">
            <div class="chalet-details">
                <h3 class="chalet-name">${chaletData.name}</h3>
                <div class="locationcontainer">
                    <img src="../images/pin.png" alt="pin Icon" class="pin-icon">
                    <p class="chalet-location">${chaletData.Location}</p>
                </div>
                <div class="chalet-footer">
                    <div class="rating">
                        <div class="stars">
                            ${ratingStars(chaletData.avg_rating || 0)}
                        </div>
                        <span class="rating-value">${
                          chaletData.avg_rating || "No ratings"
                        }</span>
                    </div>  
                    <div class="price">$${chaletData.price} /night</div>
                </div>
            </div>
        </div>
    `;
}

async function detailView(chaletId) {
  const [chaletDetails, reviews, images] = await Promise.all([
    fetchChaletDetails(chaletId),
    fetchChaletReviews(chaletId),
    fetchChaletImages(chaletId),
  ]);

  if (!chaletDetails) {
    return `<div class="error">Error loading chalet details</div>`;
  }

  const commentsHTML = reviews
    .map(
      (comment) => `
            <div class="comment">
                <div class="comment-header">
                    <span class="comment-user">${
                      comment.FirstName || "User"
                    }</span>
                    <div class="comment-rating">${ratingStars(
                      comment.rating
                    )}</div>
                    <span class="comment-date">${new Date(
                      comment.created_at
                    ).toLocaleDateString()}</span>
                </div>
                <p class="comment-text">${comment.comment}</p>
            </div>
            `
    )
    .join("");
  const imageList =
    images.length > 0
      ? images.map((img) => img.image_path)
      : ["../images/golden/"];

  return `
        <div class="modal-overlay" id="detailModal">
            <div class="modal-content">
                <button class="modal-close" onclick="closeModal()">&times;</button>
                
                <div class="modal-header">
                    <h2>${chaletDetails.name}</h2>
                    <div class="modal-rating">
                        <div class="stars">
                            ${ratingStars(chaletDetails.avg_rating || 0)}
                        </div>
                        <span class="rating-text">${
                          chaletDetails.avg_rating || "No ratings"
                        } (${reviews.length} reviews)</span>
                    </div>
                </div>

                <div class="modal-body">
                    <div class="modal-images">
                        <div class="main-image">
                            <img src="${imageList[0]}" alt="${
    chaletDetails.name
  }" id="mainImage">
                        </div>
                        <div class="thumbnail-images">
                            ${imageList
                              .map(
                                (img, index) => `
                                    <img src="${img}" alt="${
                                  chaletDetails.name
                                } ${index + 1}" 
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
                            <p>${chaletDetails.description}</p>
                        </div>

                        <div class="comments-section">
                            <h3>Customer Reviews</h3>
                            ${
                              reviews.length > 0
                                ? `
                                <div class="comments-list">
                                    ${commentsHTML}
                                </div>
                            `
                                : `<p>No reviews yet.</p>`
                            }
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

async function openDetailModal(cardId) {
  const modalHTML = await detailView(cardId);

  const existingModal = document.getElementById("detailModal");
  if (existingModal) {
    existingModal.remove();
  }

  document.body.insertAdjacentHTML("beforeend", modalHTML);
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

function renderCards() {
  renderFilteredChalets(dbChalets);

  const container = document.getElementById("chaletscont");

  container.innerHTML = `
        <div class="loading">
            <div class="spinner"></div>
            Loading Chalets...
        </div>
    `;

  setTimeout(() => {
    if (dbChalets && dbChalets.length > 0) {
      const cardsHTML = dbChalets
        .map((chalet) => renderChalet(chalet))
        .join("");
      container.innerHTML = cardsHTML;
      addCardEventListeners();

      const cards = document.querySelectorAll(".chalet");
      cards.forEach((card, index) => {
        card.style.opacity = "0";
        card.style.transform = "translateY(10px)";
        setTimeout(() => {
          card.style.transition = "opacity 0.3s ease, transform 0.3s ease";
          card.style.opacity = "1";
          card.style.transform = "translateY(0)";
        }, index * 100);
      });
    } else {
      container.innerHTML = `
                <div class="no-chalets">
                    <i class="fas fa-home" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                    <h3>No Chalets Available</h3>
                    <p>Check back later for new listings.</p>
                </div>
            `;
    }
  }, 1000);
}

function addCardEventListeners() {
  const cards = document.querySelectorAll(".chalet");

  cards.forEach((card) => {
    card.addEventListener("click", () => {
      const cardId = card.getAttribute("data-id");
      window.location.href = `chaletDetails.php?id=${cardId}`;
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
function filterChalets() {
  const priceFilter = document.getElementById("priceFilter").value;
  const ratingFilter = document.getElementById("ratingFilter").value;

  let filteredChalets = [...dbChalets];

  switch (priceFilter) {
    case "price-100":
      filteredChalets = filteredChalets.filter((chalet) => chalet.price < 100);
      break;
    case "price-200":
      filteredChalets = filteredChalets.filter(
        (chalet) => chalet.price >= 100 && chalet.price < 200
      );
      break;
    case "price-300":
      filteredChalets = filteredChalets.filter(
        (chalet) => chalet.price >= 200 && chalet.price < 300
      );
      break;
    case "price-300+":
      filteredChalets = filteredChalets.filter((chalet) => chalet.price >= 300);
      break;
    case "all":
    default:
      break;
  }

  switch (ratingFilter) {
    case "rating-2":
      filteredChalets = filteredChalets.filter(
        (chalet) => (chalet.avg_rating || 0) >= 2.0
      );
      break;
    case "rating-3":
      filteredChalets = filteredChalets.filter(
        (chalet) => (chalet.avg_rating || 0) >= 3.0
      );
      break;
    case "rating-4":
      filteredChalets = filteredChalets.filter(
        (chalet) => (chalet.avg_rating || 0) >= 4.0
      );
      break;
    case "rating-5":
      filteredChalets = filteredChalets.filter(
        (chalet) => (chalet.avg_rating || 0) >= 4.8
      );
      break;
    case "all":
    default:
      break;
  }

  renderFilteredChalets(filteredChalets);
}

function clearFilters() {
  document.getElementById("priceFilter").value = "all";
  document.getElementById("ratingFilter").value = "all";
  renderFilteredChalets(dbChalets);
}

function renderFilteredChalets(chalets) {
  const container = document.getElementById("chaletscont");

  if (chalets.length > 0) {
    const cardsHTML = chalets.map((chalet) => renderChalet(chalet)).join("");
    container.innerHTML = cardsHTML;
    addCardEventListeners();

    const cards = document.querySelectorAll(".chalet");
    cards.forEach((card, index) => {
      card.style.opacity = "0";
      card.style.transform = "translateY(10px)";
      setTimeout(() => {
        card.style.transition = "opacity 0.3s ease, transform 0.3s ease";
        card.style.opacity = "1";
        card.style.transform = "translateY(0)";
      }, index * 50);
    });
  } else {
    container.innerHTML = `
            <div class="no-chalets">
                <i class="fas fa-search" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                <h3>No Chalets Found</h3>
                <p>Try adjusting your filters or check back later for new listings.</p>
            </div>
        `;
  }
}
