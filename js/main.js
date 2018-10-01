$(function () {
    navigate('dashboard.html');
});

function navigate(page, data) {
    $.get(`pages/${page}`, function (template) {
        var rendered = Mustache.render(template, data);
        $('#container').html(rendered);
    });
}