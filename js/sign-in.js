function sign_in() {
    const data = {
        usuario: $("#txtUsuario").val(),
        password: $("#txtPassword").val()
    };

    if (!data.usuario || !data.password) {
        alert('Llene todos los datos');
        return;
    }

    request('users', 'signin', data).done(function (result) {
        const user = result.response.user;
        localStorage.setItem('user.id', user.id);
        localStorage.setItem('user.usuario', user.username);
        localStorage.setItem('user.name', user.fullname);

        $(".logged").show();
        $(".noUser").hide();

        navigate('dashboard.html');
    });
}