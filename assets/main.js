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
            console.log(response);
            const error = response.responseJSON.error || '';
            if (response.responseJSON.code === 400) {
                alert(error.errstr);
            }
            else if(response.responseJSON.code === 500){
                alert('An error ocurred. Contact support.');
                console.error(error.message);
            }
        }
    });
}