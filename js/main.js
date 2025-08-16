function signin() {
  window.location.href = "./html/login.php";
}
<<<<<<< HEAD

function signup() {
  window.location.href = "./html/signup.php";
=======
function signup() {
  window.location.href = "./html/signup.html";
>>>>>>> 4070f6e97eded006a51ecd0f5cbdfd707db3101c
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

document.addEventListener("DOMContentLoaded", () => {
  renderCards("chaletscont", chaletData, 3, "./html/");
  initswiper();
});
