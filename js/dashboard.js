$(function () {

    if (localStorage.getItem('user.id')) {
        if (localStorage.getItem('user.validation') === '1') {
            $(".logged").show();
            $(".noUser").hide();
            $("#username").html(localStorage.getItem('user.name'));
            $("#nick").html(localStorage.getItem('user.usuario'));
        }
        else {
            navigate('pending-validation.html');
        }
    }
    else {
        $(".logged").hide();
        $(".noUser").show();
        navigate('sign-in.html');
    }

    fetch_complaints();
    request('topics', 'fetch').done(result => {
        const topics = result.response.topics;
        $.each(topics, function (i, topic) {
            $("#selectTopic").append(`
            <option value="${topic.id}">${topic.name}</option>
            `);
            $("#filterTopic").append(`
            <option value="${topic.id}">${topic.name}</option>
            `);
        });
        $("#selectTopic").select2({
            placeholder: "Seleccione un tema",
            width: '155px'
        });
        $("#filterTopic").select2({
            placeholder: "Filtrar por tema ",
            width: '155px'
        }).on('change', function () {
            fetch_complaints({topics: $(this).val()});
        });
    });
    request('complaints','trending').done(result=>{
        const trending = result.response.trending;
        $.each(trending,function(hashtag,count){
            $("#trending").append(`
                    <a href="${hashtag}" onclick="navigate('dashboard.html');" class="list-group-item text-ellipsis">
                        <span class="w-8 rounded m-r-sm success"></span>
                        <span>${hashtag}</span>
                    </a>
        `);
        });
    });
    request('users', 'fetch').done(result => {
        const users = result.response.users;
        let usersCount = 0;
        $.each(users, function (i, user) {
            $("#users").append(`
                    <a class="list-group-item text-ellipsis">
                        <span class="w-8 rounded m-r-sm success"></span>
                        <span>${user.username}</span>
                    </a>
            `);
            usersCount++;
        });
        $("#countUsers").html(usersCount);
    });
});

function fetch_complaints(filters) {
    $("#quejas").html('');
    request('complaints', 'fetch', {
        usuario: {id: localStorage.getItem('user.id')},
        hashtag: window.location.hash.substr(1),
        filters: filters
    }).done(result => {
        const hashtag = window.location.hash.substr(1);
        const complaints = result.response.complaints;
        let complaintsCount = 0;
        complaints.forEach(complaint => {
            const data = {
                id: complaint.id,
                tema: {name: complaint.topic},
                usuario: {
                    name: complaint.username
                },
                productos: complaint.products,
                mensaje: complaint.message,
                fecha: complaint.date,
                acciones: complaint.actions === '1'
            };
            loadComplaint(data);
            complaintsCount++;
        });
        $("#count").html(complaintsCount);
        if (hashtag) {
            const $hashtag = $("#hashtag");
            $hashtag.html("#" + hashtag);
            $hashtag.parent().show();
        }
    });
}

function publish() {
    const data = {
        mensaje: $("#txtQueja").val(),
        tema: {id: $("#selectTopic").val(), name: $("#selectTopic option:selected").text()},
        usuario: {
            id: localStorage.getItem('user.id'),
            name: localStorage.getItem('user.usuario')
        }
    };

    if (!data.mensaje) {
        alert('Ingrese un mensaje');
        return;
    }
    if (!data.tema.id) {
        alert('Elija un tema');
        return;
    }

    request('complaints', 'publish', data).done(result =>{
        data.id = result.response.id;
        navigate('dashboard.html');
        //loadComplaint(data);
    });
}

function loadComplaint(data) {
    $.get(`templates/queja.html`, function (template) {
        const rendered = Mustache.render(template, data);

        const mensaje = ($(rendered).find('.mensaje').html()).replace(/(#\w+)\b/g, `<a href="$1" class="hashtag" onclick="navigate('dashboard.html');">$1</a>`);

        $("#quejas").prepend($(rendered).get(0));
        $(`#quejas .mensaje[data-id=${data.id}]`).html(mensaje);

        $("#txtQueja").val('');
        $("#txtMoroso").val('');

        $("#alertNoPosts").hide();
    });
}

function editComplaint() {
    const data = {
        id: $('#modal-edit-complaint').find('.modal-footer .submit').data('id'),
        mensaje: $("#txtMensajeEditado").val(),
        usuario: {
            id: localStorage.getItem('user.id')
        }
    };

    if (!data.mensaje) {
        return;
    }

    if (confirm('¿Esta seguro?')) {
        request('complaints', 'edit', data).done(() => {
            $('#modal-edit-complaint').modal('hide');
            navigate('dashboard.html');
        });
    }
}

function deleteComplaint() {
    const data = {
        id: $('#modal-delete-complaint').find('.modal-footer .submit').data('id'),
        razon: $("#txtRazonEliminar").val(),
        usuario: {
            id: localStorage.getItem('user.id')
        }
    };

    if (!data.razon) {
        return;
    }

    if (confirm('¿Esta seguro?')) {
        request('complaints', 'delete', data).done(() => {
            $('#modal-delete-complaint').modal('hide');
            navigate('dashboard.html');
        });
    }
}