
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
        $('html').animate({ scrollTop: 0 }, 'slow');
        return;
    }

    commentUrl = $(e.currentTarget).attr("href")

    fetch(commentUrl, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }  
    })
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
                    $('html').animate({ scrollTop: 0 }, 'slow');
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
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
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
                    $('html').animate({ scrollTop: 0 }, 'slow');
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
let contentFormHolder = $("#content-form-holder")  // conteneur du formulaire
let contentUrl; // URL utilisé pour dialoguer avec le serveur

function generateContentForm(e) {
    e.preventDefault() // Prevention du comportement natif
    contentUrl = $(e.currentTarget).attr("href") // On definit la variable d'URL qui sera appelé
    fetch(contentUrl, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest' // Sécurité pour verification si la requète viens de JS
        }  
    })
        .then(res => res.json()) // On indique à JS de traiter la réponse en JSON
        .then(res => {
            // console.log(res)
            switch (res.status) {
                case 'success-form': // arrivé du formulaire
                    $(contentFormHolder).html(res.data) // On place le contenu data de la réponse dans le conteneur
                    $(contentFormHolder).slideDown("slow") // On ouvre le conteneur avec un annimation
                    break;

                case 'error':   // erreur de la génération du formulaire
                    $("#alert-container").html( 
                        "<div id='error-alert' class='alert alert-danger alert-dismissible fade show text-center' role='alert'>" +
                        res.data + // Le message data est placé dans une boite d'erreur
                        "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                        "<span aria-hidden='true'>&times;</span>" +
                        "</button>" +
                        "</div>"
                    )
                    $('html').animate({ scrollTop: 0 }, 'slow'); // On défile la page pour que l'utilisateur puisse voir l'erreur
                    break;
            }
        })
        .catch(function (err) {
            // console.log(err)
        })
}

function submitContent(e) {
    e.preventDefault()

    fetch(contentUrl, { // On utilise la variable définit au-dessus pour envoyer le formulaire
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        method: "POST", 
        body: new FormData(document.getElementById("content_form")) 
        // on met les données du formulaire dans un objet FormData
    })
        .then(res => res.json())
        .then(res => {
            console.log(res)
            switch (res.status) {
                case 'success':
                    $("#content-part").html(res.data)
                    $(contentFormHolder).slideUp("slow", function () { // on repli le conteneur du formulaire
                        $(contentFormHolder).html("") // On vide le conteneur du formulaire
                    })
                    break;

                case 'success-form':
                    $(contentFormHolder).html(res.data) // une erreur de validation, le formulaire est renvoyé
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
                    $('html').animate({ scrollTop: 0 }, 'slow');
                    break;
            }
        })
        .catch(function (err) {
            // console.log(err)
        })
}

function cancelContentForm(e) {
    e.preventDefault()
    $(contentFormHolder).slideUp("slow", function () {
        $(contentFormHolder).html("")
    })
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

// -------------- GESTION AFFICHAGE REPONSES --------------
let lastClicked = 0

function showAnswers(commentId) {
    let delay = 600
    if(Date.now() - lastClicked < delay) return;

    if ($("#answers-"+commentId).hasClass('open-answers')) {
        $("#answers-"+commentId).slideUp(delay)
        $("#answers-"+commentId).removeClass('open-answers')
    }else{
        $('.open-answers').slideUp(delay)
        $('.open-answers').removeClass('open-answers')
        $("#answers-"+commentId).addClass('open-answers')
        $("#answers-"+commentId).slideDown(delay)
    }
    lastClicked = Date.now()
}
// -------------- GESTION AFFICHAGE REPONSES --------------

// -------------- GESTION CONFIRM --------------
$(".playlist-delete").confirm({
    title: 'Confirmation',
    content: 'Etes vous sûr de vouloir supprimer cette playlist ?',
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

$(".content-delete").confirm({
    title: 'Confirmation',
    content: 'Etes vous sûr de vouloir supprimer ce contenu ?',
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

$(".delete-comment").confirm({
    title: 'Confirmation',
    content: 'Etes vous sûr de vouloir supprimer ce commentaire ?',
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