$(function () {
    if (localStorage.getItem('user.id')) {
        $(".logged").show();
        $(".noUser").hide();
    }
    else {
        $(".logged").hide();
        $(".noUser").show();
        navigate('sign-in.html');
    }
    request('complaints', 'fetch').done(result => {
        const complaints = result.response.complaints;
        complaints.forEach(function (complaint) {
            const data = {
                mensaje: complaint.message,
                moroso: complaint.payer,
                usuario: {
                    name: complaint.username
                },
                fecha: complaint.date
            };
            loadComplaint(data);
        });
    });
});

function publish() {
    const data = {
        mensaje: $("#txtQueja").val(),
        moroso: $("#txtMoroso").val(),
        usuario: {
            id: localStorage.getItem('user.id'),
            name: localStorage.getItem('user.name')
        }
    };

    if (!data.moroso) {
        alert('Ingrese un nombre');
        return;
    }
    if (!data.mensaje) {
        alert('Ingrese un mensaje');
        return;
    }

    request('complaints', 'publish', data).done(() => {
        loadComplaint(data);
    });
}

function loadComplaint(data) {
    $.get(`templates/queja.html`, function (template) {
        var rendered = Mustache.render(template, data);
        $("#quejas").prepend(rendered);

        $("#txtQueja").val('');
        $("#txtMoroso").val('');

        $("#alertNoPosts").hide();
    });
}