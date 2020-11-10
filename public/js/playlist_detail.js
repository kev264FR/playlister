
// ----------- GESTION POST NOUVEAU COMMENTAIRE ------------------
let commentFormHolder = $("#comment-form-holder")
    
// Permet de rendre le formulaire draggable
    commentFormHolder.draggable() 

let commentUrl

// Generation du formulaire de post de commentaire
function generateCommentForm(e) {
    e.preventDefault()
    commentUrl = $(e.currentTarget).attr("href")

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

// ----------- GESTION AJOUT CONTENT -------------------------
let contentFormHolder = $("#content-form-holder")
let contentUrl;

function generateContentForm(e) {
    e.preventDefault()
    contentUrl = $(e.currentTarget).attr("href")

    fetch(contentUrl)
        .then(res => res.json())
        .then(res => {
            switch (res.status) {
                case "form":  $(contentFormHolder).html(res.data)
                    break;
                
                case false: console.log(res.data)
                    break;
            }
        })
        .catch(function (err) {
            console.log(err)
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
            switch (res.status) {
                
                case "form":  
                            $(contentFormHolder).html(res.data)
                    break;
                
                case "add": 
                            $("#content-part").html(res.data)
                            $(contentFormHolder).html("")
                    break;
                
                case false: console.log(res.data)
                    break;
            }
        })
        .catch(function (err) {
            console.log(err)
        })
}


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