$(function () {
    session_valid().then(sessionValid => {
        if (localStorage.getItem('user.id') && sessionValid) {
            if (localStorage.getItem('user.validation') === '1') {
                $(".logged").show();
                $(".noUser").hide();
                $("#username").html(localStorage.getItem('user.name'));
                $("#nick").html(localStorage.getItem('user.usuario'));

                var params = window.location.pathname.split('/').slice(1);
                var p = params[0];

                navigate(p || 'dashboard');
            } else {
                navigate('pending-validation');
            }
        } else {
            $(".logged").hide();
            $(".noUser").show();
            navigate('sign-in');
        }
    });
});

async function session_valid() {
    if (!localStorage.getItem('session.time')) {
        return false;
    }
    const result = await request('users', 'validatesession', {
        id: localStorage.getItem('user.id'),
        time: localStorage.getItem('session.time')
    });
    return result.response;
}

function sign_out() {
    if (confirm('¿Desea cerrar la sesión?')) {
        localStorage.clear();
        navigate('sign-in');
        $(".logged").hide();
        $(".noUser").show();
    }
}

function navigate(page, data) {
    history.pushState({}, null, '/');
    $.get(`pages/${page}.html`, function (template) {
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