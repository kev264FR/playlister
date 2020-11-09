
// ----------- GESTION POST NOUVEAU COMMENTAIRE ------------------
let commentFormHolder = $("#comment-form-holder")
    
// Permet de rendre le formulaire draggable
    commentFormHolder.draggable() 

let commentUrl

// Generation du formulaire de post de commentaire
function generateForm(e) {
    e.preventDefault()
    let button = $(e.currentTarget)
    commentUrl = button.attr("href")

    fetch(commentUrl)
        .then(res => res.json())
        .then(res => {
            commentFormHolder.html(res)

        })
        .catch(function (err) {
            console.log(err)
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
            $("#comment-part").html(res)
        })
        .catch(function (err) {
            console.log(err)
        })
}

// Ferme la fenertre de formulaire
function cancelComment(e) {
    e.preventDefault()
    commentFormHolder.html("")
    commentFormHolder.removeAttr("style")
}
// ----------- GESTION POST NOUVEAU COMMENTAIRE ------------------


// -------------- GESTION MODAL IFRAME --------------
function handleClick(e) {
    e.preventDefault()
    let player = $(".player iframe")
    player.attr("src", $(e.currentTarget).data("url"))
    $("#embed").slideDown("slow")
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
                location.href = this.currentTarget.attr('href');
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