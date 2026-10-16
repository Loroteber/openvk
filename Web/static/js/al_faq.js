function hideAnswer(question) {
    $(question).toggleClass("active");
    $(question).next('div').slideToggle(300);
}