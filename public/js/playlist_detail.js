
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

function generateContentForm(e) {
    e.preventDefault() // Prevention du comportement natif
    let contentUrl = $(e.currentTarget).attr("href") // On definit la variable d'URL qui sera appelé
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
                    $(contentFormHolder).slideDown("slow") // On ouvre le conteneur avec une animation
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
let lastClicked = 0 // initialisation de la variable 

function showAnswers(commentId) {
    let delay = 600 // définition du délai entre 2 clicks en ms
    if(Date.now() - lastClicked < delay) return; // Si cliques trop rapide on ne fait rien 

    if ($("#answers-"+commentId).hasClass('open-answers')) {  // verification si groupe ouvert 
        $("#answers-"+commentId).slideUp(delay)    // fermeture du groupe et définition de la vitesse 
        $("#answers-"+commentId).removeClass('open-answers') // On retire la classe 
    }else{
        $('.open-answers').slideUp(delay)   // Fermeture de tous les groupes ouverts
        $('.open-answers').removeClass('open-answers')  // Retrait de la classe de tous les groupes
        $("#answers-"+commentId).addClass('open-answers') // Ajout de la classe au groupe visé 
        $("#answers-"+commentId).slideDown(delay)   // Ouverture du groupe 
    }
    lastClicked = Date.now()    // on récupère la date pour le prochain appel 
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