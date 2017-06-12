$(document).ready(function () {
    runScript();

    $("#switch").click(function () {
        if(localStorage.getItem("state") != null) {
            $("#panel").hide().slideDown('slow');
            localStorage.removeItem("state");
        } else {
            localStorage.setItem("state", 1);
            $("#panel").show().slideUp('slow');
        }
        runScript();
    });
});

function runScript() {
    if(localStorage.getItem("state") != null) {
        $("#panel").addClass("hide");
        $("#switch").html("+");
        $("#panel").hide();
    } else {
        $("#switch").html("-");
        $("#panel").show();
    }
}