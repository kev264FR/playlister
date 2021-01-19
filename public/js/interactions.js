function handleLike(e) {
    e.preventDefault()
    let btnId = '#' + $(e.currentTarget).attr('id')

    fetch($(e.currentTarget).attr("href"), {
        headers: {
            "X-Requested-With": "XMLHttpRequest",
        }  
    })
        .then(res => res.json())
        .then(res => {
            // console.log(res)
            switch (res.status) {
                case 'success':
                    if (res.data == 'like') {
                        $(btnId).removeClass('btn-outline-dark')
                        $(btnId).addClass('btn-dark')
                    } else {
                        $(btnId).removeClass('btn-dark')
                        $(btnId).addClass('btn-outline-dark')
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
                    $('html').animate({ scrollTop: 0 }, 'slow');
                    break;
            }
        })
        .catch(function (err) {
            // console.log(err)
        })
}

function handleFollowPlaylist(e) {
    e.preventDefault()
    let btnId = '#' + $(e.currentTarget).attr('id')
    let followedPlaylistsCount;


    fetch($(e.currentTarget).attr("href"), {
        headers: {
            "X-Requested-With": "XMLHttpRequest",
        }  
    })
        .then(res => res.json())
        .then(res => {
            // console.log(err)
            switch (res.status) {
                case 'success':
                    if (res.data == 'follow') {
                        $(btnId).removeClass('btn-outline-dark')
                        $(btnId).addClass('btn-dark')
                    } else {
                        $('#followed-playlist-' + res.id).hide()
                        $('#followed-playlist-' + res.id).removeClass('show-playlist')
                        $(btnId).removeClass('btn-dark')
                        $(btnId).addClass('btn-outline-dark')

                        followedPlaylistsCount = $('#followed-playlist-part').children('.show-playlist').length
                        if (followedPlaylistsCount == 0) {
                            $('#followed-playlist-part').replaceWith('<p class="text-center"> Vous ne suivez aucune playlist.</p>')
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
                    $('html').animate({ scrollTop: 0 }, 'slow');
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
    
    fetch($(e.currentTarget).attr("href"), {
        headers: {
            "X-Requested-With": "XMLHttpRequest",
        }  
    })
        .then(res => res.json())
        .then(res => {
            // console.log(res)
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
                    $('html').animate({ scrollTop: 0 }, 'slow');
                    break;
            }
        })
        .catch(function (err) {
            // console.log(err)
        })
}

