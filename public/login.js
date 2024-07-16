document.addEventListener("DOMContentLoaded", function() {
    const loginButton = document.getElementById("login_button_desktop");
    const loginButtonHeader = document.getElementById("login_button_desktop_header");
    const loginForm = document.querySelector(".login-form");
    const retourButton = document.querySelector(".closebutton_modal");

    loginButton.addEventListener("click", displayLoginForm);
    loginButtonHeader.addEventListener("click", displayLoginForm)
    retourButton.addEventListener("click", closeLoginForm);

    function displayLoginForm () {
        loginForm.style.display = "block";
    }

    function closeLoginForm () {
        loginForm.style.display = "none";
    }

    let slideIndex = 0;
    const slides = document.querySelectorAll('.carousel-item');
    let autoSlideInterval;

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.remove('active');
        });
        slides[index].classList.add('active');
    }

    function nextSlide() {
        slideIndex = (slideIndex + 1) % slides.length;
        showSlide(slideIndex);
    }


    function startAutoSlide() {
        autoSlideInterval = setInterval(nextSlide, 3000);
    }

    function stopAutoSlide() {
        clearInterval(autoSlideInterval);
    }

    document.querySelector('.carousel').addEventListener('mouseenter', stopAutoSlide);
    document.querySelector('.carousel').addEventListener('mouseleave', startAutoSlide);

    showSlide(slideIndex);
    startAutoSlide();

    window.nextSlide = nextSlide;
});
