var int_slide = 0; //Set current slide index
var list_slides = document.querySelectorAll('ul.slider li'); //Get a list which has a new element for each slide
var int_size = list_slides.length;

function updateSlide(number) {
    list_slides[int_slide].classList.remove('visible'); //Remove current visible slide
    if (number > int_size - 1) { //If at the end of slides, reset to beginning//
        int_slide = 0
    } else if (number < 0) {
        int_slide = int_size - 1;
    } else { //Otherwise go to the next slide in list//
        int_slide = number;
    }
    list_slides[int_slide].classList.add('visible'); //Make the new slide visible

}

function autoUpdate() {
    updateSlide(int_slide + 1);
}

setInterval(autoUpdate, 10000);
document.getElementById('prev').addEventListener('click', function () { //If previous button is clicked, go to previous slide//
    updateSlide(int_slide - 1)
});
document.getElementById('next').addEventListener('click', function () {//If next button is clicked, go to next slide//
    updateSlide(int_slide + 1)
});