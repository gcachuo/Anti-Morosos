Project.Admin = {};

Project.Admin.validateUser = function (id, validate) {
    Project.request('admin', 'validateuser', {
        id: localStorage.getItem('user.id'),
        user_id: id,
        validate: validate
    }).then(() => {
        Project.navigate('admin');
    });
};