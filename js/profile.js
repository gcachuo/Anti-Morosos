function update_password() {
    $("#btnUpdatePass").prop('disabled', true).find('i').addClass('fa-spinner');
    if ($("#txtNewPass").val() !== $("#txtVerifyPass").val()) {
        alert('Las contraseñas no coinciden');
        return;
    }
    request('users', 'changepassword',
        {
            id: localStorage.getItem('user.id'),
            password: $("#txtPassword").val(),
            newpass: $("#txtVerifyPass").val()
        }
    ).done(() => {
        alert('Contraseña cambiada correctamente');
        navigate('profile.html');
    }).always(() => {
        $("#btnUpdatePass").prop('disabled', false).find('i').removeClass('fa-spinner');
    });
}