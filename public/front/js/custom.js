const link = document.getElementById("style");
//link.href = `front/css/style.css?v=${new Date().getTime()}`;

$(document).ready(function () {
    $(".testimonial-carousel").slick({
        dots: false,
        infinite: true,
        speed: 600,
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 5000,
        arrows: true,
        fade: false,
        cssEase: "ease-in-out",
        pauseOnHover: true,
        pauseOnFocus: true,
        adaptiveHeight: true,
    });
});

// // Toggle sidebar open/close
// document.querySelector('.menu-toogle').addEventListener('click', function () {
//     document.querySelector('.sidebar').classList.toggle('expand');
// });

// // Close button removes expand
// document.querySelector('.close-sidebar').addEventListener('click', function () {
//     document.querySelector('.sidebar').classList.remove('expand');
// });

const menuToggle = document.querySelector(".menu-toogle");
const sidebar = document.querySelector(".sidebar");
const closeSidebar = document.querySelector(".close-sidebar");

if (menuToggle && sidebar) {
    menuToggle.addEventListener("click", function () {
        sidebar.classList.toggle("expand");
    });
}

if (closeSidebar && sidebar) {
    closeSidebar.addEventListener("click", function () {
        sidebar.classList.remove("expand");
    });
}
