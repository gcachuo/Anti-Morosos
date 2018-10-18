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
    request('complaints', 'fetch', {
        hashtag: window.location.hash.substr(1)
    }).done(result => {
        const hashtag = window.location.hash.substr(1);
        const complaints = result.response.complaints;
        let complaintsCount = 0;
        complaints.forEach(function (complaint) {
            const data = {
                id: complaint.id,
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
        if (hashtag) {
            $("#tema").html("#" + hashtag);
            $("#tema").parent().show();
        }
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

    request('complaints', 'publish', data).done(result =>{
        data.id = result.response.id;
        loadComplaint(data);
    });
}

function loadComplaint(data) {
    $.get(`templates/queja.html`, function (template) {
        var rendered = Mustache.render(template, data);

        const mensaje = ($(rendered).find('.mensaje').html()).replace(/(#\w+)\b/g, `<a href="$1" class="hashtag" onclick="navigate('dashboard.html');">$1</a>`);

        $("#quejas").prepend($(rendered).get(0));
        $(`#quejas .mensaje[data-id=${data.id}]`).html(mensaje);

        $("#txtQueja").val('');
        $("#txtMoroso").val('');

        $("#alertNoPosts").hide();
    });
}