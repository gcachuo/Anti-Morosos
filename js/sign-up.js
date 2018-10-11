function sign_up() {
    const data = {
        nombre: $("#txtNombre").val(),
        usuario: $("#txtUsuario").val(),
        password: $("#txtPassword").val(),
        verifyPass: $("#txtVerifyPass").val()
    };

    if (!data.nombre || !data.usuario || !data.password) {
        alert('Llene todos los campos');
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