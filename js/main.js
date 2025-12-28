// ===== MOBILE MENU TOGGLE =====
const menuBtn = document.getElementById("menu-btn");
const navLinks = document.getElementById("nav-links");

menuBtn.addEventListener("click", () => {
  navLinks.classList.toggle("active");
});

// ===== SWIPER SLIDER FOR "PICK YOUR DREAM CAR" =====
const swiper = new Swiper(".swiper", {
  slidesPerView: 1,
  spaceBetween: 20,
  loop: true,
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },
  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },
  breakpoints: {
    640: {
      slidesPerView: 2,
      spaceBetween: 20,
    },
    1024: {
      slidesPerView: 3,
      spaceBetween: 30,
    },
  },
});

// ===== OPTIONAL: SCROLL REVEAL ANIMATION =====
if (window.ScrollReveal) {
  ScrollReveal().reveal(".section__container, .range__card, .select__card", {
    distance: "50px",
    duration: 1000,
    easing: "ease-in-out",
    origin: "bottom",
    interval: 100,
  });
}
