function handleLike(e) {
    e.preventDefault()
    let counter = $(e.currentTarget).children("span")

    fetch($(e.currentTarget).attr("href"))
        .then(res => res.json())
        .then(res => {
            switch (res) {
                case null: console.log("error")
                    break;

                case true: $(counter).html(+counter.html()+1)
                    break;

                case false: $(counter).html(+counter.html()-1)
                    break;
            
                default: location.href = res
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
            switch (res) {
                case null: console.log("error")
                    break;

                case true: $(counter).html(+counter.html()+1)
                    break;

                case false: $(counter).html(+counter.html()-1)
                    break;
            
                default: location.href = res
                    break;
            }
        })
        .catch(function (err) {
            console.log(err)
        })
}

function handleFollowUser(e) {
    e.preventDefault()
    console.log(e.currentTarget)
    
    fetch($(e.currentTarget).attr("href"))
        .then(res => res.json())
        .then(res => {
            switch (res) {
                case null: console.log("error")
                    break;

                case true: console.log("added")
                    break;

                case false: console.log("removed")
                    break;
            
                default: location.href = res
                    break;
            }
        })
        .catch(function (err) {
            console.log(err)
        })
}