function handleLike(e) {
    e.preventDefault()
    let counter = $(e.currentTarget).children("span")

    fetch($(e.currentTarget).attr("href"))
        .then(res => res.json())
        .then(res => {
            if (res === null) {
                console.log("error")
            }
            if (res === true) {
                $(counter).html(+counter.html()+1)
            }
            if (res === false) {
                $(counter).html(+counter.html()-1)
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
            if (res === null) {
                console.log("error")
            }
            if (res === true) {
                $(counter).html(+counter.html()+1)
            }
            if (res === false) {
                $(counter).html(+counter.html()-1)
            }

        })
        .catch(function (err) {
            console.log(err)
        })
}

function handleFollowUser(e) {
    e.preventDefault()
    console.log(e.currentTarget)
    // fetch()
    //     .then(res => res.json())
    //     .then(res => {
            

    //     })
    //     .catch(function (err) {
    //         console.log(err)
    //     })
}