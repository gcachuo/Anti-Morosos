<?php
/**
 * Created by PhpStorm.
 * User: memo
 * Date: 13/11/18
 * Time: 04:07 AM
 */

namespace Controller;

use JsonResponse;
use System;

class Admin
{
    function fetchusers()
    {
        System::allowed_methods(['GET']);

        $user_id = System::isset_get($_GET['id']);
        $this->validate($user_id);

        $Users = new \Model\Users();
        $users = $Users->selectUsersWithValidation($user_id);

        foreach ($users as $key => $user) {
            $users[$key] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'validation' => $user['validation'] == "1"
            ];
        }

        return compact('users');
    }

    function validateuser()
    {
        System::allowed_methods(['GET']);

        $user_id = System::isset_get($_GET['id']);
        $validate_user_id = System::isset_get($_GET['user_id']);
        $validate = (int)System::isset_get($_GET['validate']);
        $this->validate($user_id);

        $Users = new \Model\Users();
        $Users->updateUserValidation($validate_user_id, $validate);
        return [];
    }

    private function validate($user_id)
    {
        $Users = new \Model\Users();
        $type = $Users->getUserType($user_id);

        if ($type == 1) {
            JsonResponse::sendResponse(['message' => 'No Permitido.']);
        }
        return [];
    }
}