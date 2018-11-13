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
select user_password password from users where user_username='$username' or user_email='$username'
sql;

        $hash = db_result($sql)['password'];
        if (!password_verify($password, $hash)) {
            set_error("El usuario o la contraseña son incorrectos.");
        }

        $sql = <<<sql
select user_id id, user_name fullname, user_username username, user_status status,user_validation validation,user_type type
from users
where user_username = '$username' or user_email='$username'
sql;
        $user = db_result($sql);
        $user['time'] = date('Y-m-d H:i:s');
        $session = password_hash(SEED . $user['id'] . $user['time'], CRYPT_BLOWFISH);
        $sql = <<<sql
update users set user_session='$session' where user_id='$user[id]';
sql;
        db_query($sql);

        return compact('user');
    }

    function signup()
    {
        $products = isset_get($_REQUEST['productos']);
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

        $user_validation = 0;
        if ($user_referrer !== 'null') {
            $sql = <<<sql
select user_id referrer from users where user_referral='$user_referrer';
sql;

            if (!db_result($sql)['referrer']) {
                set_error('La referencia es inválida.');
            }

            $user_referrer = db_result($sql)['referrer'];
            $user_validation = 1;
        }

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
                   user_referral,
                   user_validation)
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
        '$user_referral',
        $user_validation)
sql;
        db_query($sql);

        $id = db_last_id();

        if (!$id) {
            db_error();
        }


        foreach ($products as $product) {
            if (!is_numeric($product)) {
                $sql = <<<sql
replace into products(product_name) values ('$product');
sql;
                db_query($sql);

                $product = db_last_id();
            }
            $sql = <<<sql
replace into users_products(product_id, user_id) VALUES ('$product','$id');
sql;
            db_query($sql);
        }
        $user = [
            "id" => $id,
            "username" => $user_username,
            "fullname" => trim("$user_name $user_lastname_1 $user_lastname_2")
        ];

        return compact('user', 'user_validation');
    }

    function changepassword()
    {
        $user_id = isset_get($_REQUEST['id']);
        $password = isset_get($_REQUEST['password']);
        $new_pass = password_hash(isset_get($_REQUEST['newpass']), CRYPT_BLOWFISH);

        if (!$password || !$new_pass) {
            set_error("Llene todos los datos");
        }

        $sql = <<<sql
select user_password password from users where user_id='$user_id'
sql;

        $hash = db_result($sql)['password'];
        if (!password_verify($password, $hash)) {
            set_error("El usuario o la contraseña son incorrectos.");
        }

        $sql = <<<sql
update users set user_password='$new_pass' where user_id='$user_id';
sql;

        db_query($sql);
    }

    function fetch()
    {
        $sql = <<<sql
select 
user_username username ,
count(c.complaint_id) count
from users u
left join complaints c on c.user_id=u.user_id and complaint_status=true
where user_status=true and u.user_id<>1 and u.user_id<>3
group by u.user_id
order by count desc ;
sql;
        $users = db_all_results($sql);
        return compact('users');
    }

    function validatesession()
    {
        $user_id = isset_get($_REQUEST['id']);
        $token = isset_get($_REQUEST['time']);

        $sql = <<<sql
select user_session token from users where user_id='$user_id'
sql;

        $hash = db_result($sql)['token'];
        if (!password_verify(SEED . $user_id . $token, $hash)) {
            return false;
        }

        return true;
    }
}