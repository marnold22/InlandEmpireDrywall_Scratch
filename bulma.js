// The following code is based off a toggle menu by @Bradcomp
// source: https://gist.github.com/Bradcomp/a9ef2ef322a8e8017443b626208999c1
(function() {
    var burger = document.querySelector('.burger');
    var menu = document.querySelector('#'+burger.dataset.target);
    burger.addEventListener('click', function() {
        burger.classList.toggle('is-active');
        menu.classList.toggle('is-active');
    });
})();


const carousels = bulmaCarousel.attach('.carousel', {
    autoplay: true,
    duration: 1500,
    loop: true,
    navigation: false,
    slidesToShow: 5,
    slidesToScroll: 2,
    breakpoints: [{ changePoint: 500, slidesToShow: 1, slidesToScroll: 1 }, { changePoint: 768, slidesToShow: 3, slidesToScroll: 3 } ]
});