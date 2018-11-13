Project.Users = {};

Project.Users.signIn = function () {
    const data = {
        usuario: $("#txtUsuario").val(),
        password: $("#txtPassword").val()
    };

    if (!data.usuario || !data.password) {
        alert('Llene todos los datos');
        return;
    }

    Project.request('users', 'signin', data).done(function (result) {
        const user = result.response.user;
        localStorage.setItem('user.id', user.id);
        localStorage.setItem('user.usuario', user.username);
        localStorage.setItem('user.name', user.fullname);
        localStorage.setItem('user.validation', user.validation);
        localStorage.setItem('user.type', user.type);
        localStorage.setItem('session.time', user.time);

        $(".logged").show();
        $(".noUser").hide();

        Project.navigate('dashboard');
    });
};

Project.Users.signUp = function () {
    const data = {
        productos: $("#selectProductos").val(),
        nombre: $("#txtNombre").val(),
        ap_paterno: $("#txtApPaterno").val(),
        ap_materno: $("#txtApMaterno").val(),
        correo: $("#txtCorreo").val(),
        usuario: $("#txtUsuario").val(),
        password: $("#txtPassword").val(),
        verifyPass: $("#txtVerifyPass").val(),
        empresa: $("#txtEmpresa").val(),
        razon_social: $("#txtRazon").val(),
        puesto: $("#txtPuesto").val(),
        telefono: $("#txtTelefono").val(),
        whatsapp: $("#txtWhatsapp").val(),
        referencia: $("#txtReferencia").val()
    };

    switch ('') {
        case data.nombre:
        case data.ap_paterno:
        case data.correo:
        case data.usuario:
        case data.password:
            // alert('Llene todos los campos requeridos');
            return;
    }

    if (!data.productos) {
        return;
    }

    if (data.password !== data.verifyPass) {
        alert('Las contraseñas no coinciden');
        return;
    }

    if (!$.isNumeric(data.referencia) && data.referencia) {
        alert('La referencia es inválida.');
        return;
    }

    Project.request('users', 'signup', data).done(function (result) {
        const user = result.response.user;
        const user_validation = result.response.user_validation;
        localStorage.setItem('user.id', user.id);
        localStorage.setItem('user.usuario', user.username);
        localStorage.setItem('user.name', user.fullname);
        localStorage.setItem('user.validation', user_validation);

        $(".logged").show();
        $(".noUser").hide();
        if (user_validation) {
            Project.navigate('dashboard');
        }
        else {
            Project.navigate('sign-in');
        }
    });
};

Project.Users.signOut = function () {
    if (confirm('¿Desea cerrar la sesión?')) {
        localStorage.clear();
        Project.navigate('sign-in');
        $(".logged").hide();
        $(".noUser").show();
    }
};

Project.Users.changePassword = function () {
    $("#btnUpdatePass").prop('disabled', true).find('i').addClass('fa-spinner');
    if ($("#txtNewPass").val() !== $("#txtVerifyPass").val()) {
        alert('Las contraseñas no coinciden');
        return;
    }
    Project.request('users', 'changepassword',
        {
            id: localStorage.getItem('user.id'),
            password: $("#txtPassword").val(),
            newpass: $("#txtVerifyPass").val()
        }
    ).done(() => {
        alert('Contraseña cambiada correctamente');
        Project.navigate('profile');
    }).always(() => {
        $("#btnUpdatePass").prop('disabled', false).find('i').removeClass('fa-spinner');
    });
};

Project.Users.validateSession = async function () {
    if (!localStorage.getItem('session.time')) {
        return false;
    }
    const result = await Project.request('users', 'validatesession', {
        id: localStorage.getItem('user.id'),
        time: localStorage.getItem('session.time')
    });
    return result.response;
};