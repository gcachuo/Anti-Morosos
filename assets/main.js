$(function () {
    navigate('dashboard.html');
});

function sign_out() {
    if (confirm('¿Desea cerrar la sesión?')) {
        localStorage.clear();
        navigate('sign-in.html');
        $(".logged").hide();
        $(".noUser").show();
    }
}

function navigate(page, data) {
    $.get(`pages/${page}`, function (template) {
        var rendered = Mustache.render(template, data);
        $('.app-body').html(rendered);
    });
}

/**
 * @param controller
 * @param action
 * @param data
 * @returns {*|{readyState, getResponseHeader, getAllResponseHeaders, setRequestHeader, overrideMimeType, statusCode, abort}}
 */
function request(controller, action, data) {
    return $.ajax({
        url: `api/${controller}/${action}`,
        method: 'GET',
        data: data,
        dataType: 'json',
        error: response => {
            console.log(response);
            const error = response.responseJSON.error || '';
            if (response.responseJSON.code === 400) {
                alert(error.message);
            }
            else if(response.responseJSON.code === 500){
                alert('An error ocurred. Contact support.');
                console.error(error.message);
            }
        }
    });
}