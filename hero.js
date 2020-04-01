class Hero {
    constructor() {
        //Set current slide index
        this.current_slide = 0;

        //Get a list of the slides available
        this.all_slides = document.querySelectorAll('ul.slider li');

        //Set the number of slides
        this.maximum_slides = this.all_slides.length;
    }

    updateSlide(slide_index) {

        //Remove current visible slide
        this.all_slides[this.current_slide].classList.remove('visible');
        //If at the end of slides
        if (slide_index > this.maximum_slides - 1) {
            //Reset to beginning
            this.current_slide = 0
        }
        //If going left at beginning of slides
        else if (slide_index < 0) {
            //Go to end of slides
            this.current_slide = this.maximum_slides - 1;
        }
        //If not at beginning or end
        else {
            //Change slide to passed index
            this.current_slide = slide_index;
        }

        //Make the new slide visible
        this.all_slides[this.current_slide].classList.add('visible');
    }
}

//When the DOM is ready, execute anonymous function
$(document).ready(function () {
    //Instantiate class
    let slider = new Hero();
    //Execute function every ten seconds
    setInterval(function () {
        //Go to next slider in hero slider
        slider.updateSlide(slider.current_slide + 1)
    }, 10000);
    //If previous button clicked
    $("#prev").on('click', function () {
        //Go to previous slider in hero slider
        slider.updateSlide(slider.current_slide - 1)
    });
    //If next button is clicked
    $("#next").on('click', function () {
        //Go to next slider in hero slider
        slider.updateSlide(slider.current_slide + 1)
    })
});
