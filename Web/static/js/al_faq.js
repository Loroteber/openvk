let issue = 0;

function hideAnswer(question) {
    $(question).toggleClass("active");
    $(question).next('div').slideToggle(300);
}

function hideIssue(item) {
    $(item).next('div').slideToggle(300);
}

function hideIssueButton(item) {
    document.cookie = `faq_closed_issues=${encodeURIComponent(JSON.stringify(issues.map(issue => issue.id)))}; path=/; max-age=31536000; SameSite=Lax`;
    $(item).parent('div').slideToggle(300);
}

function changeIssue(backward = false) {
    issue = (issue + (backward ? -1 : 1) + issues.length) % issues.length;

    $("#faq-issue h1").text(issues[issue].title);
    $("#faq-issue p").html(issues[issue].description);   
}

function faqSettings() {
    showUntranslated = document.cookie.split("; ").find(c => c.startsWith("faq_show_untranslated="))?.split("=")[1];
    new MessageBox(
        tr("ui_settings_window"),
        `
            <center>
                <label>
                    <input type="checkbox" id="untranslated" ${showUntranslated == "1" ? "checked" : ""}/>
                    ${tr("support_faq_show_untranslated")}
                </label>
            </center>
        `,
        [
            tr("close"),
            tr("apply")
        ],
        [
            ()=>{},
            ()=>{
                document.cookie = `faq_show_untranslated=${$("#untranslated").is(":checked") ? "1" : "0"}`;
                location.reload();
            }
        ]
    );
}