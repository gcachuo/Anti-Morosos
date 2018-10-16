$(function () {
    if (localStorage.getItem('user.id')) {
        $(".logged").show();
        $(".noUser").hide();
        $("#username").html(localStorage.getItem('user.name'));
        $("#nick").html(localStorage.getItem('user.usuario'));
    }
    else {
        $(".logged").hide();
        $(".noUser").show();
        navigate('sign-in.html');
    }
    request('complaints', 'fetch').done(result => {
        const complaints = result.response.complaints;
        let complaintsCount = 0;
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
            complaintsCount++;
        });
        $("#count").html(complaintsCount);
    });
});

function publish() {
    const data = {
        mensaje: $("#txtQueja").val(),
        moroso: $("#txtMoroso").val(),
        usuario: {
            id: localStorage.getItem('user.id'),
            name: localStorage.getItem('user.usuario')
        }
    };

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