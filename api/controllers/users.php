<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 11/10/18
 * Time: 01:49 AM
 */

class users
{
    function signin()
    {
        $username = isset_get($_REQUEST['usuario']);
        $password = isset_get($_REQUEST['password']);

        if (!$username || !$password) {
            set_error("Llene todos los datos");
        }

        $sql = <<<sql
select user_password password from users where user_username='$username'
sql;

        $hash = db_result($sql)['password'];
        if (!password_verify($password, $hash)) {
            set_error("El usuario o la contraseña son incorrectos.");
        }

        $sql = <<<sql
select user_id id, user_fullname fullname, user_username username, user_status status
from users
where user_username = '$username'
sql;
        $user = db_result($sql);
        return compact('user');
    }

    function signup()
    {
        $fullname = isset_get($_REQUEST['nombre']);
        $username = isset_get($_REQUEST['usuario']);
        $password = isset_get($_REQUEST['password']);
        $verify = isset_get($_REQUEST['verifyPass']);

        if (!$fullname || !$username || !$password) {
            set_error('Llene todos los datos');
        } else if ($password !== $verify) {
            set_error('Las contraseñas no coinciden.');
        }

        $password = password_hash($password, CRYPT_BLOWFISH);

        $sql = <<<sql
insert into users(user_fullname, user_username, user_password) VALUES ('$fullname','$username','$password')
sql;
        db_query($sql);

        $user = [
            "id" => db_last_id(),
            "username" => $fullname,
            "fullname" => $username
        ];
        return compact('user');
    }

    function changepassword()
    {
        $id = isset_get($_REQUEST['id']);
        $password = isset_get($_REQUEST['password']);

        $password = password_hash($password, CRYPT_BLOWFISH);

        if (!$id || !$password) {
            set_error("Llene todos los datos");
        }

        $sql = <<<sql
update users set user_password='$password' where user_id='$id';
sql;

        db_query($sql);
    }
}