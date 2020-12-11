
// ----------- GESTION POST NOUVEAU COMMENTAIRE ------------------
let commentFormHolder
let commentFormStatus = 0
let commentUrl

// Generation du formulaire de post de commentaire
function generateCommentForm(e) {
    e.preventDefault()

    if (!commentFormStatus) {
        if ($(e.currentTarget).data("collection")) {
            commentFormHolder = $("#comment-form-holder-" + $(e.currentTarget).data("collection"))
        } else {
            commentFormHolder = $("#comment-form-holder")
        }
    } else {
        $("#alert-container").html(
            "<div id='error-alert' class='alert alert-danger alert-dismissible fade show text-center' role='alert'>" +
            "Un seul commentaire a la fois" +
            "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
            "<span aria-hidden='true'>&times;</span>" +
            "</button>" +
            "</div>"
        )
        return;
    }

    commentUrl = $(e.currentTarget).attr("href")

    fetch(commentUrl)
        .then(res => res.json())
        .then(res => {
            // console.log(res)
            switch (res.status) {
                case 'success-form':

                    $(commentFormHolder).html(res.data)
                    commentFormStatus = 1
                    break;

                case 'error':
                    $("#alert-container").html(
                        "<div id='error-alert' class='alert alert-danger alert-dismissible fade show text-center' role='alert'>" +
                        res.data +
                        "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                        "<span aria-hidden='true'>&times;</span>" +
                        "</button>" +
                        "</div>"
                    )
                    break;
            }

        })
        .catch(function (err) {
            // console.log(err)
        })
}

// Envoi les données du formulaire 
function submitComment(e) {
    e.preventDefault()

    fetch(commentUrl, {
        method: "POST",
        body: new FormData(document.getElementById("comment_form"))
    })
        .then(res => res.json())
        .then(res => {
            // console.log(res)
            switch (res.status) {
                case 'success':
                    $("#comment-part").html(res.data)
                    commentFormStatus = 0
                    break;

                case 'success-form':
                    $(commentFormHolder).html(res.data)
                    break;

                case 'error':
                    $("#alert-container").html(
                        "<div id='error-alert' class='alert alert-danger alert-dismissible fade show text-center' role='alert'>" +
                        res.data +
                        "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                        "<span aria-hidden='true'>&times;</span>" +
                        "</button>" +
                        "</div>"
                    )
                    break;
            }
        })
        .catch(function (err) {
            // console.log(err)
        })
}

// Ferme la fenertre de formulaire
function cancelComment(e) {
    e.preventDefault()
    commentFormHolder.html("")
    commentFormStatus = 0
}
// ----------- GESTION POST NOUVEAU COMMENTAIRE ------------------

// ----------- GESTION AJOUT CONTENT -------------------------
let contentFormHolder = $("#content-form-holder")
let contentUrl;

function generateContentForm(e) {
    e.preventDefault()
    contentUrl = $(e.currentTarget).attr("href")
    fetch(contentUrl)
        .then(res => res.json())
        .then(res => {
            // console.log(err)
            switch (res.status) {
                case 'success-form':
                    $(contentFormHolder).html(res.data)
                    $(contentFormHolder).slideDown("slow")
                    break;

                case 'error':
                    $("#alert-container").html(
                        "<div id='error-alert' class='alert alert-danger alert-dismissible fade show text-center' role='alert'>" +
                        res.data +
                        "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                        "<span aria-hidden='true'>&times;</span>" +
                        "</button>" +
                        "</div>"
                    )
                    break;
            }
        })
        .catch(function (err) {
            // console.log(err)
        })
}

function submitContent(e) {
    e.preventDefault()

    fetch(contentUrl, {
        method: "POST",
        body: new FormData(document.getElementById("content_form"))
    })
        .then(res => res.json())
        .then(res => {
            // console.log(res)
            switch (res.status) {
                case 'success':

                    $("#content-part").html(res.data)
                    $(contentFormHolder).slideUp("slow", function () {
                        $(contentFormHolder).html("")
                    })
                    break;
                case 'success-form':
                    $(contentFormHolder).html(res.data)
                    break;

                case 'error':
                    $("#alert-container").html(
                        "<div id='error-alert' class='alert alert-danger alert-dismissible fade show text-center' role='alert'>" +
                        res.data +
                        "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                        "<span aria-hidden='true'>&times;</span>" +
                        "</button>" +
                        "</div>"
                    )
                    break;
            }
        })
        .catch(function (err) {
            // console.log(err)
        })
}

function cancelContentForm(e) {
    e.preventDefault()
    contentFormHolder.html("")
}


// -------------- GESTION MODAL IFRAME --------------
function handleClick(e) {
    e.preventDefault()
    let player = $(".player iframe")
    player.attr("src", $(e.currentTarget).data("url"))
    $("#embed").slideDown("slow")
    $('html').animate({ scrollTop: 100 }, 'slow');
}
// -------------- GESTION MODAL IFRAME --------------

// -------------- GESTION CONFIRM --------------
$(".btn-danger").confirm({
    title: 'Confirmation',
    content: 'Etes vous sûr de vouloir supprimer ?',
    theme: 'supervan',
    type: 'green',
    buttons: {
        ok: {
            text: "Continuer",
            action: function () {
                location.href = this.$target.attr('href');
            }
        },
        cancel: {
            text: "Retour",
            action: function () {
                console.log('the user clicked cancel');
            }
        }
    }
})
// -------------- GESTION CONFIRM --------------