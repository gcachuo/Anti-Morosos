function go_back() {
    localStorage.clear();
    $(".logged").hide();
    $(".noUser").show();
    navigate('sign-in.html');
}