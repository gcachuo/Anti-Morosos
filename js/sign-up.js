function sign_up() {
    const data = {
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
        whatsapp: $("#txtWhatsapp").val()
    };

    switch (false) {
        case data.nombre:
        case data.ap_paterno:
        case data.ap_materno:
        case data.correo:
        case data.usuario:
        case data.password:
            alert('Llene todos los campos requeridos');
            return;
    }

    if (data.password !== data.verifyPass) {
        alert('Las contraseñas no coinciden');
        return;
    }

    request('users', 'signup', data).done(function (result) {
        const user = result.response.user;
        localStorage.setItem('user.id', user.id);
        localStorage.setItem('user.usuario', user.username);
        localStorage.setItem('user.name', user.fullname);

        $(".logged").show();
        $(".noUser").hide();

        navigate('dashboard.html');
    });
}