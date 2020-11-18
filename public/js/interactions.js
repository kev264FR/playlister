function handleLike(e) {
    e.preventDefault()
    let counter = $(e.currentTarget).children("span")

    fetch($(e.currentTarget).attr("href"))
        .then(res => res.json())
        .then(res => {
            switch (res.status) {
                case 'success': 
                                if (res.data == 'like') {
                                    $(counter).html(+counter.html()+1)
                                }else{
                                    $(counter).html(+counter.html()-1)
                                }
                    break;
                    
                case 'error': 
                                $("#alert-container").html(
                                    "<div id='error-alert' class='alert alert-danger alert-dismissible fade show text-center' role='alert'>"+
                                        res.data+
                                        "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>"+
                                            "<span aria-hidden='true'>&times;</span>"+
                                        "</button>"+
                                    "</div>"
                                )
                    break;
            }
        })
        .catch(function (err) {
            console.log(err)
        })
}

function handleFollowPlaylist(e) {
    e.preventDefault()
    let counter = $(e.currentTarget).children("span")
    
    fetch($(e.currentTarget).attr("href"))
        .then(res => res.json())
        .then(res => {
            switch (res.status) {
                case 'success': 
                                if (res.data == 'follow') {
                                    $(counter).html(+counter.html()+1)
                                }else{
                                    $(counter).html(+counter.html()-1)
                                }
                    break;
                    
                case 'error': 
                                $("#alert-container").html(
                                    "<div id='error-alert' class='alert alert-danger alert-dismissible fade show text-center' role='alert'>"+
                                        res.data+
                                        "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>"+
                                            "<span aria-hidden='true'>&times;</span>"+
                                        "</button>"+
                                    "</div>"
                                )
                    break;
            }
        })
        .catch(function (err) {
            console.log(err)
        })
}

function handleFollowUser(e) {
    e.preventDefault()
    
    fetch($(e.currentTarget).attr("href"))
        .then(res => res.json())
        .then(res => {
            switch (res.status) {
                // case 'success': 
                //                 if (res.data == 'follow') {
                //                     $(counter).html(+counter.html()+1)
                //                 }else{
                //                     $(counter).html(+counter.html()-1)
                //                 }
                //     break;
                    
                case 'error': 
                                $("#alert-container").html(
                                    "<div id='error-alert' class='alert alert-danger alert-dismissible fade show text-center' role='alert'>"+
                                        res.data+
                                        "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>"+
                                            "<span aria-hidden='true'>&times;</span>"+
                                        "</button>"+
                                    "</div>"
                                )
                    break;
            }
        })
        .catch(function (err) {
            console.log(err)
        })
}