Project = {};
require('./_complaints');
require('./_products');
require('./_users');
require('./_admin');

Project.navigate = function (page, data) {
   //history.pushState({}, null, '/');
    $.get(`pages/${page}.html`, function (template) {
        const rendered = Mustache.render(template, data||{});
        $('.app-body').html(rendered);
    });
};

/**
 * @param controller
 * @param action
 * @param data
 * @returns {*|{readyState, getResponseHeader, getAllResponseHeaders, setRequestHeader, overrideMimeType, statusCode, abort}}
 */
Project.request = function (controller, action, data) {
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
            else if (response.responseJSON.code === 500) {
                alert('An error ocurred. Contact support.');
                console.error(error.message);
            }
        }
    });
};

module.exports = Project;