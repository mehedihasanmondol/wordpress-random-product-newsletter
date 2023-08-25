// SLIDERS SIMPLE-FADE
// parámetros: (claseSlider, intervalo)
$(document).ready(function () {
    $(".notice-dismiss").click(function () {
        $(this).closest(".notice.is-dismissible").remove();
    })

});