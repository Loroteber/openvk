let issue = 0;

function hideAnswer(question) {
    $(question).toggleClass("active");
    $(question).next('div').slideToggle(300);
}

function hideIssue(item) {
    $(item).next('div').slideToggle(300);
}

function hideIssueButton(item) {
    document.cookie = `closed_issues=${encodeURIComponent(JSON.stringify(issues.map(issue => issue.id)))}; path=/; max-age=31536000; SameSite=Lax`;
    $(item).parent('div').slideToggle(300);
}

function changeIssue(backward = false) {
    issue = (issue + (backward ? -1 : 1) + issues.length) % issues.length;

    $("#faq-issue h1").text(issues[issue].title);
    $("#faq-issue p").html(issues[issue].description);   
}