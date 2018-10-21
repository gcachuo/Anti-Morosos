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
        $productos = isset_get($_REQUEST['productos']);
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
        $user_referrer = isset_get($_REQUEST['referencia'], 'null');

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

        do {
            $user_referral = str_pad(rand(0, 9999), 4, '0');
            $sql = <<<sql
select count(1) count from users where user_referral='$user_referral';
sql;
        } while (db_result($sql)['count']);

        $sql = <<<sql
select count(1) count from users where user_username='$user_username';
sql;

        if (db_result($sql)['count']) {
            set_error('El usuario ya existe.');
        }

        $sql = <<<sql
select user_id referrer from users where user_referral='$user_referrer';
sql;

        if (!db_result($sql)['referrer']) {
            set_error('La referencia es inválida.');
        }

        $user_referrer = db_result($sql)['referrer'];

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
                   user_whatsapp,
                   user_referrer,
                   user_referral)
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
        '$user_whatsapp',
        $user_referrer,
        '$user_referral')
sql;
        db_query($sql);

        $id = db_last_id();

        if (!$id) {
            db_error();
        }

        foreach ($productos as $producto) {
            $sql = <<<sql
replace into users_products(id_product, id_user) VALUES ('$producto','$id');
sql;
            db_query($sql);
        }
        $user = [
            "id" => $id,
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