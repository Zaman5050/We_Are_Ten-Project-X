
const allTime = document.getElementById("allTime");
// const thisMonth = document.getElementById("thisMonth");
const thisQuarter = document.getElementById("thisQuarter");
const thisYear = document.getElementById("thisYear");
const custom = document.getElementById("custom");

if(allTime){
    allTime.style.background = "#FFDC5F"
    allTime.style.color = "#1A1B1F"
}

allTime && allTime.addEventListener('click', function() {
    allTime.style.background = "#FFDC5F"
    allTime.style.color = "#1A1B1F"
    // thisMonth.style.background="transparent"
    // thisMonth.style.color="#252C32"
    thisQuarter.style.background = "transparent"
    thisQuarter.style.color = "#1A1B1F"
    thisYear.style.background = "transparent"
    thisYear.style.color = "#1A1B1F"
    custom.style.background = "transparent"
    custom.style.color = "#1A1B1"
});

// thisMonth.addEventListener('click',function(){
// thisMonth.style.background="#252C32"
// thisMonth.style.color="#FFF"
// allTime.style.background="transparent"
// allTime.style.color="#252C32"
// thisQuarter.style.background="transparent"
// thisQuarter.style.color="#252C32"
// thisYear.style.background="transparent"
// thisYear.style.color="#252C32"
// custom.style.background="transparent"
// custom.style.color="#252C32"
// });
thisQuarter && thisQuarter.addEventListener('click', function() {
    thisQuarter.style.background = "#FFDC5F"
    thisQuarter.style.color = "#1A1B1F"
    allTime.style.background = "transparent"
    allTime.style.color = "#1A1B1F"
    // thisMonth.style.background="transparent"
    // thisMonth.style.color="#252C32"
    thisYear.style.background = "transparent"
    thisYear.style.color = "#1A1B1F"
    custom.style.background = "transparent"
    custom.style.color = "#1A1B1F"
});
thisYear && thisYear.addEventListener('click', function() {
    thisYear.style.background = "#FFDC5F"
    thisYear.style.color = "#fff"
    allTime.style.background = "transparent"
    allTime.style.color = "#1A1B1F"
    // thisMonth.style.background="transparent"
    // thisMonth.style.color="#252C32"
    thisQuarter.style.background = "transparent"
    thisQuarter.style.color = "#1A1B1F"
    custom.style.background = "transparent"
    custom.style.color = "#1A1B1F"
});
custom && custom.addEventListener('click', function() {
    custom.style.background = "#FFDC5F"
    custom.style.color = "#fff"
    allTime.style.background = "transparent"
    allTime.style.color = "#1A1B1F"
    // thisMonth.style.background="transparent"
    // thisMonth.style.color="#252C32"
    thisQuarter.style.background = "transparent"
    thisQuarter.style.color = "#1A1B1F"
    thisYear.style.background = "transparent"
    thisYear.style.color = "#1A1B1F"
});


var flatpickrSelect;

$(document).ready(function() {


    $('#side-sub-menu').click(function() {
        $('#side-sub-menu-container').toggle();
        $('#sidebar-arrow').toggleClass('sidebar-submenue-arrow')
    })
    $('#side-sub-menu-setting').click(function() {
        $('#side-sub-menu-container-setting').toggle();
        $('#sidebar-arrow-setting').toggleClass('sidebar-submenue-arrow')
    })
})




document.querySelectorAll('.container').forEach(container => {
    const input = container.querySelector('.percentageInput');
    const circle = container.querySelector('.circle');

    input && input.addEventListener('input', function() {
        // Get the input value
        const percentage = Math.min(100, Math.max(0, this
        .value)); // Ensure the percentage is between 0 and 100

        // Update the circle's data-fill attribute and text
        circle.setAttribute('data-fill', percentage);
        // Assuming there's an element to show the percentage inside the circle, e.g., with class 'percentage'
        const percentageText = circle.querySelector('.percentage');
        if (percentageText) {
            percentageText.innerText = `${percentage}%`;
        }

        // Update the color based on the percentage
        const color = getColorBasedOnPercentage(percentage);
        circle.style.setProperty('--color', color);
    });
});

setTimeout(function() {
    var alert = document.querySelector('.alert-success');
    if (alert) {
        alert.style.transition = "opacity 0.5s";
        alert.style.opacity = 0;
        setTimeout(function() {
            alert.remove();
        }, 3000);
    }
}, 4000);
