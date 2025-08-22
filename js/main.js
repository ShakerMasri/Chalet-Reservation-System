function signin() {
  window.location.href = "./html/login.php";
}

function signup() {
  window.location.href = "./html/signup.php";
}

function initswiper() {
  const swiper = document.getElementById("swiper-wrapper");
  const prev = document.getElementById("prev");
  const next = document.getElementById("next");
  let index = 0;

  setInterval(() => {
    const total = swiper.children.length;
    if (total === 0) return;
    index = (index + 1) % total;
    updateSwiper();
  }, 5000);

  next.addEventListener("click", () => {
    const total = swiper.children.length;
    index = (index - 1 + total) % total;
    updateSwiper();
  });

  prev.addEventListener("click", () => {
    const total = swiper.children.length;
    index = (index + 1 + total) % total;
    updateSwiper();
  });

  function updateSwiper() {
    swiper.style.transform = `translateX(-${index * 100}%)`;
  }
}

document.addEventListener("DOMContentLoaded", () => {
  initswiper();
});
const track = document.querySelector(".chalets-track");
const cards = document.querySelectorAll(".chalet-card");

cards.forEach((card) => {
  const clone = card.cloneNode(true);
  track.appendChild(clone);
});

let position = 0;
const cardWidth = 300;
const gap = 20;
const totalCards = document.querySelectorAll(".chalet-card").length;
const visibleCards = Math.floor(
  track.parentElement.offsetWidth / (cardWidth + gap)
);
const maxPosition = -(totalCards - visibleCards) * (cardWidth + gap);

function autoSlide() {
  position -= cardWidth + gap;

  if (Math.abs(position) >= (totalCards * (cardWidth + gap)) / 2) {
    position = 0;
    track.style.transition = "none";
    track.style.transform = `translateX(${position}px)`;
    setTimeout(() => {
      track.style.transition = "transform 0.5s ease-in-out";
    });
  } else {
    track.style.transform = `translateX(${position}px)`;
  }
}

let slideInterval = setInterval(autoSlide, 5000);

track.parentElement.addEventListener("mouseenter", () =>
  clearInterval(slideInterval)
);
track.parentElement.addEventListener("mouseleave", () => {
  slideInterval = setInterval(autoSlide, 5000);
});
