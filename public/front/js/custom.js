// const link = document.getElementById('style');
// link.href = `css/style.css?v=${new Date().getTime()}`;

$(document).ready(function () {
    $('.testimonial-carousel').slick({
        dots: false,
        infinite: true,
        speed: 600,
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 5000,
        arrows: true,
        fade: false,
        cssEase: 'ease-in-out',
        pauseOnHover: true,
        pauseOnFocus: true,
        adaptiveHeight: true
    });
});
