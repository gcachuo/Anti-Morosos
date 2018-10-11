$(function () {
    if (localStorage.getItem('user.id')) {
        $(".logged").show();
        $(".noUser").hide();
        navigate('dashboard.html');
    }
    else {
        $(".logged").hide();
        $(".noUser").show();
        navigate('sign-in.html');
    }
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

    $.get(`templates/queja.html`, function (template) {
        var rendered = Mustache.render(template, data);
        $("#quejas").prepend(rendered);

        $("#txtQueja").val('');
        $("#txtMoroso").val('');

        $("#alertNoPosts").hide();
    });
}