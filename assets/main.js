function navigate(page, data) {
    $.get(`pages/${page}`, function (template) {
        var rendered = Mustache.render(template, data);
        $('#container').html(rendered);
    });
}

function request(controller, action, data) {
    return $.ajax({
        url: `api/index.php?controller=${controller}&action=${action}`,
        method: 'GET',
        data: data,
        dataType: 'json',
        error: response => {
            const error = response.responseJSON.error;
            if (response.responseJSON.code === 501) {
                alert(error.errstr);
            }
            console.log(error);
        }
    });
}