$(".delete-account").confirm({
    title: 'Confirmation',
    content: 'Etes vous sûr de vouloir supprimer le compte, cette action ne peut être inversé !!',
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

    $(".ban").confirm({
    title: 'Confirmation',
    content: 'Etes vous sûr de vouloir bannir ce compte !!',
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

    $(".unban").confirm({
    title: 'Confirmation',
    content: 'Etes vous sûr de vouloir dé-bannir ce compte !',
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

    $(".remove-admin").confirm({
    title: 'Confirmation',
    content: 'Etes vous sûr de vouloir retirer le role admin !',
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

    $(".add-admin").confirm({
    title: 'Confirmation',
    content: 'Etes vous sûr de vouloir ajouter le role admin !',
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