$(function () {
    request('products', 'fetch').done(result => {
        const products = result.response.products;

        $.each(products, function (i, product) {
            $("#selectProductos").append(`<option value="${product.id}">${product.name}</option>`);
        });

        $("#selectProductos").select2({
            placeholder: "Productos y Servicios",
            width: '100%',
            tags: true
        });
    });
});

function sign_up() {
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

    request('users', 'signup', data).done(function (result) {
        const user = result.response.user;
        const user_validation = result.response.user_validation;
        localStorage.setItem('user.id', user.id);
        localStorage.setItem('user.usuario', user.username);
        localStorage.setItem('user.name', user.fullname);
        localStorage.setItem('user.validation', user_validation);

        $(".logged").show();
        $(".noUser").hide();
        if (user_validation) {
            navigate('dashboard');
        }
        else {
            navigate('sign-in');
        }
    });
}