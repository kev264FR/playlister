function handleLike(e) {
    e.preventDefault()
    let counter = $(e.currentTarget).children("span")
    let btnId = '#' + $(e.currentTarget).attr('id')

    fetch($(e.currentTarget).attr("href"))
        .then(res => res.json())
        .then(res => {
            // console.log(err)
            switch (res.status) {
                case 'success':
                    if (res.data == 'like') {
                        $(counter).html(+counter.html() + 1)
                        $(btnId).removeClass('btn-primary')
                        $(btnId).addClass('btn-success')
                    } else {
                        $(counter).html(+counter.html() - 1)
                        $(btnId).removeClass('btn-success')
                        $(btnId).addClass('btn-primary')
                    }
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
// {{ include("interaction/btn_group.html.twig", {playlist: playlist}) }}
function handleFollowPlaylist(e) {
    e.preventDefault()
    let counter = $(e.currentTarget).children("span")
    let btnId = '#' + $(e.currentTarget).attr('id')
    let followedPlaylistsCount;


    fetch($(e.currentTarget).attr("href"))
        .then(res => res.json())
        .then(res => {
            // console.log(err)
            switch (res.status) {
                case 'success':
                    if (res.data == 'follow') {
                        $(counter).html(+counter.html() + 1)
                        $(btnId).removeClass('btn-primary')
                        $(btnId).addClass('btn-success')
                    } else {
                        $(counter).html(+counter.html() - 1)
                        $('#followed-playlist-' + res.id).hide()
                        $('#followed-playlist-' + res.id).removeClass('show-playlist')
                        $(btnId).removeClass('btn-success')
                        $(btnId).addClass('btn-primary')

                        followedPlaylistsCount = $('#followed-playlist-part').children('.show-playlist').length
                        if (followedPlaylistsCount == 0) {
                            $('#followed-playlist-part').replaceWith('<p class="text-center"> Vous ne suivez aucune playliste.</p>')
                        }
                    }
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

function handleFollowUser(e) {
    e.preventDefault()
    let followedUsersCount;

    fetch($(e.currentTarget).attr("href"))
        .then(res => res.json())
        .then(res => {
            // console.log(err)
            switch (res.status) {
                case 'success':
                    if (res.data == "follow") {
                        $(e.target).text('Ne plus suivre')
                    } else {
                        $(e.target).text('Suivre')
                        $('#followed-user-' + res.id).hide()
                        $('#followed-user-' + res.id).removeClass('show-playlist')

                        followedUsersCount = $('#followed-user-part').children('.show-playlist').length
                        if (followedUsersCount == 0) {
                            $('#followed-user-part').replaceWith('<p class="text-center">Vous ne suivez personne.</p>')
                        }
                    }
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


function goBack(e) {
    e.preventDefault()
    window.history.back()
}

