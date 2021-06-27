$(() => {
    $(".qcard-comments-toggle").click(function () {
        if ($(this).hasClass("hide")) {
            $(this).removeClass("hide");
            $(this).find("span").text("Ocultar comentarios");
            $(this).find("i").removeClass("fa-eye").addClass("fa-eye-slash");
            $(".qcard-comments").removeAttr("hidden");

        } else {
            $(this).addClass("hide");
            $(this).find("span").text("Mostrar comentarios");
            $(this).find("i").removeClass("fa-eye-slash").addClass("fa-eye");
            $(".qcard-comments").attr("hidden", true);
        }
    });

    $(".qcard-details-button").click(function () {
        if(!$(this).hasClass("clicked")){
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    $(".qcard-code pre").append(" geo:"+position.coords.latitude + "," +position.coords.longitude);
                    $(this).text("Contenido filtrado según tu ubicación actual.");
                    $(this).addClass("clicked");
                    $(this).css("cursor","unset");
                });
            }
        }
    });

    $("#code-button").click(function () {
        if ($(this).hasClass("hide")) {
            let search_query = $(".qcard-code pre").text();

            ShodanAPI.search(search_query);
        }
    });

    $(".qcard-info button").click(function () {
        ShodaninAPI.create_comment(
            $(".qcard").attr("id").split("_")[1],
            $("#comment-textarea").val());
    });

    $(".q-upvote").click(function () {
        if (!$(this).hasClass("clicked")) {
            let qid = $(this).parent().parent().parent().attr("id").split("_")[1];

            ShodaninAPI.upvote_query(qid);
        }
    });

    $(".q-downvote").click(function () {
        if (!$(this).hasClass("clicked")) {
            let qid = $(this).parent().parent().parent().attr("id").split("_")[1];

            ShodaninAPI.downvote_query(qid);
        }
    });

    $(".c-upvote").click(function () {
        if (!$(this).hasClass("clicked")) {
            let cid = $(this).parent().parent().attr("id").split("_")[1];

            ShodaninAPI.upvote_comment(cid);
        }
    });

    $("#navbar-menu-button").click(function() {
        if ($(this).hasClass("hide")) {
            $(this).removeClass("hide");
            $("nav:not(.landing) li:not(:first-child)").addClass("force-visible");

        } else {
            $(this).addClass("hide");
            $("nav:not(.landing) li:not(:first-child)").removeClass("force-visible");
        }
    });
});