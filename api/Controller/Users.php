<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 11/10/18
 * Time: 01:49 AM
 */

namespace Controller;

use JsonResponse;
use System;

class Users
{
    function signin()
    {
        $username = System::isset_get($_POST['usuario']);
        $password = System::isset_get($_POST['password']);

        System::check_value_empty(['usuario' => $username, 'password' => $password], ['usuario', 'password'], 'Llene todos los datos');

        $Users = new \Model\Users();
        $hash = $Users->getPasswordHash($username);

        if (!password_verify($password, $hash)) {
            JsonResponse::sendResponse(['message' => "El usuario o la contraseña son incorrectos."]);
        }

        $user = $Users->getUser($username);

        $Users->setUserSession($user);

        return compact('user');
    }

    function signup()
    {
        $products = System::isset_get($_POST['productos']);
        $user_name = System::isset_get($_POST['nombre']);
        $user_lastname_1 = System::isset_get($_POST['ap_paterno']);
        $user_lastname_2 = System::isset_get($_POST['ap_materno']);
        $user_email = trim(System::isset_get($_POST['correo']));
        $user_username = trim(System::isset_get($_POST['usuario']));
        $user_password = System::isset_get($_POST['password']);
        $password_verify = System::isset_get($_POST['verifyPass']);
        $user_company = System::isset_get($_POST['empresa']);
        $user_business_name = System::isset_get($_POST['razon_social']);
        $user_business_position = System::isset_get($_POST['puesto']);
        $user_business_phone = System::isset_get($_POST['telefono']);
        $user_whatsapp = System::isset_get($_POST['whatsapp']);
        $user_referrer = System::isset_get($_POST['referencia'], 'null');

        System::check_value_empty(['nombre' => $user_name, 'ap_paterno' => $user_lastname_1, 'correo' => $user_email, 'usuario' => $user_username, 'password' => $user_password, 'verifyPass' => $password_verify], ['nombre', 'ap_paterno', 'correo', 'usuario', 'password', 'verifyPass'], 'Llene todos los datos');

        if ($user_password !== $password_verify) {
            JsonResponse::sendResponse(['message' => 'Las contraseñas no coinciden.']);
        }

        $user_password = password_hash($user_password, CRYPT_BLOWFISH);

        $Users = new \Model\Users();

        $user_referral = $Users->setReferralCode();

        $exists = $Users->userExists($user_username);
        if ($exists) {
            JsonResponse::sendResponse(['message' => 'El usuario ya existe.']);
        }

        $user_validation = $Users->getUserReferrer($user_referrer);

        $user_id = $Users->insertUser($user_email, $user_username, $user_password, $user_name, $user_lastname_1, $user_lastname_2, $user_company, $user_business_name, $user_business_position, $user_business_phone, $user_whatsapp, $user_referrer, $user_referral, $user_validation);

        $Products = new \Model\Products();
        $Products->insertProducts($products, $user_id);

        $user = [
            "id" => $user_id,
            "username" => $user_username,
            "fullname" => trim("$user_name $user_lastname_1 $user_lastname_2")
        ];

        return compact('user', 'user_validation');
    }

    function changepassword()
    {
        $user_id = System::isset_get($_POST['id']);
        $password = System::isset_get($_POST['password']);
        $new_pass = System::isset_get($_POST['newpass']);

        System::check_value_empty(['id' => $user_id, 'password' => $password, 'newpass' => $new_pass], ['id', 'password', 'newpass'], "Llene todos los datos");

        $new_pass = password_hash($new_pass, CRYPT_BLOWFISH);

        $Users = new \Model\Users();
        $hash = $Users->getPasswordHashFromId($user_id);

        if (!password_verify($password, $hash)) {
            JsonResponse::sendResponse(['message' => 'El usuario o la contraseña son incorrectos.']);
        }

        $Users->updatePassword($user_id, $new_pass);
        return [];
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