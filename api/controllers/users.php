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
select user_id id, user_name fullname, user_username username, user_status status
from users
where user_username = '$username'
sql;
        $user = db_result($sql);
        return compact('user');
    }

    function signup()
    {
        $user_name = isset_get($_REQUEST['nombre']);
        $user_lastname_1 = isset_get($_REQUEST['ap_paterno']);
        $user_lastname_2 = isset_get($_REQUEST['ap_materno']);
        $user_email = trim(isset_get($_REQUEST['correo']));
        $user_username = trim(isset_get($_REQUEST['usuario']));
        $user_password = isset_get($_REQUEST['password']);
        $password_verify = isset_get($_REQUEST['verifyPass']);
        $user_company = isset_get($_REQUEST['empresa']);
        $user_business_name = isset_get($_REQUEST['razon_social']);
        $user_business_position = isset_get($_REQUEST['puesto']);
        $user_business_phone = isset_get($_REQUEST['telefono']);
        $user_whatsapp = isset_get($_REQUEST['whatsapp']);

        switch (false) {
            case $user_name:
            case $user_lastname_1:
            case $user_email:
            case $user_username:
            case $user_password:
                set_error('Llene todos los datos');
                break;
        }

        if ($user_password !== $password_verify) {
            set_error('Las contraseñas no coinciden.');
        }

        $user_password = password_hash($user_password, CRYPT_BLOWFISH);

        $sql = <<<sql
insert into users (user_email,
                   user_username,
                   user_password,
                   user_name,
                   user_lastname_1,
                   user_lastname_2,
                   user_company,
                   user_business_name,
                   user_business_position,
                   user_business_phone,
                   user_whatsapp)
VALUES ('$user_email',
        '$user_username',
        '$user_password',
        '$user_name',
        '$user_lastname_1',
        '$user_lastname_2',
        '$user_company',
        '$user_business_name',
        '$user_business_position',
        '$user_business_phone',
        '$user_whatsapp')
sql;
        db_query($sql);

        $user = [
            "id" => db_last_id(),
            "username" => $user_username,
            "fullname" => trim("$user_name $user_lastname_1 $user_lastname_2")
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