<?php
/**
 * Created by PhpStorm.
 * User: memo
 * Date: 13/11/18
 * Time: 04:07 AM
 */

class admin
{
    function validate($user_id)
    {
        $sql = <<<sql
select user_type type from users where user_id='$user_id';
sql;
        $type = db_result($sql)['type'];
        if ($type == 1) {
            set_error('No Permitido.');
        }
    }

    function fetchusers()
    {
        $user_id = isset_get($_GET['id']);
        $this->validate($user_id);

        $sql = <<<sql
select user_id id,user_name name, user_validation validation from users where user_status=true and user_id<>'$user_id';
sql;
        $users = db_all_results($sql);

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
        $user_id = isset_get($_GET['id']);
        $validate_user_id = isset_get($_GET['user_id']);
        $validate = isset_get($_GET['validate']);
        $this->validate($user_id);

        $sql = <<<sql
update users set user_validation=$validate where user_id='$validate_user_id'
sql;
        db_query($sql);
    }
}