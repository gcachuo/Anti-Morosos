Project.Admin = {};

Project.Admin.validateUser = function (id) {
    Project.request('admin', 'validateuser', {
        id: localStorage.getItem('user.id'),
        user_id: id
    }).then(() => {
        Project.navigate('admin');
    });
};