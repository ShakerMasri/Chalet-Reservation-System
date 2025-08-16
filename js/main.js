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
