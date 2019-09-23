<?php


namespace Model;


use System;

class Users
{
    public function __construct()
    {
        $mysql = new MySQL();
        $mysql->create_table('users', [
            new TableColumn('user_id', ColumnTypes::BIGINT, 20, true, null, true, true),
            new TableColumn('user_email', ColumnTypes::VARCHAR, 100),
            new TableColumn('user_username', ColumnTypes::VARCHAR, 100),
            new TableColumn('user_password', ColumnTypes::VARCHAR, 100),
            new TableColumn('user_name', ColumnTypes::VARCHAR, 100),
            new TableColumn('user_lastname_1', ColumnTypes::VARCHAR, 100),
            new TableColumn('user_lastname_2', ColumnTypes::VARCHAR, 100),
            new TableColumn('user_company', ColumnTypes::VARCHAR, 100),
            new TableColumn('user_business_name', ColumnTypes::VARCHAR, 100),
            new TableColumn('user_business_position', ColumnTypes::VARCHAR, 100),
            new TableColumn('user_business_phone', ColumnTypes::VARCHAR, 100),
            new TableColumn('user_whatsapp', ColumnTypes::VARCHAR, 100),
            new TableColumn('user_referrer', ColumnTypes::BIGINT, 20),
            new TableColumn('user_referral', ColumnTypes::BIGINT, 20),
            new TableColumn('user_validation', ColumnTypes::BIT, 1, false, "b'1'"),
            new TableColumn('user_status', ColumnTypes::BIT, 1, false, "b'1'"),
            new TableColumn('user_session', ColumnTypes::VARCHAR, 255),
            new TableColumn('user_type', ColumnTypes::INT, 11),
        ], <<<sql
create unique index users_user_email_uindex on users (user_email);
create unique index users_user_referral_uindex on users (user_referral);
create unique index users_user_username_uindex on users (user_username);
create index users_users_user_id_fk on users (user_referrer);
sql
        );
    }

    public function getPasswordHash($username)
    {
        $sql = <<<sql
select user_password password from users where user_username=? or user_email=?
sql;

        $mysql = new MySQL();
        $mysqli_result = $mysql->prepare($sql, ['ss', $username, $username]);
        return System::isset_get($mysql->fetch_single($mysqli_result)['password']);
    }

    public function getUser($username)
    {
        $sql = <<<sql
select user_id id, user_name fullname, user_username username, user_status status,user_validation validation,user_type type
from users
where user_username = ? or user_email=?
sql;
        $mysql = new MySQL();
        $user = $mysql->fetch_single($mysql->prepare($sql, ['ss', $username, $username]));
        $user['time'] = date('Y-m-d H:i:s');

        return $user;
    }

    public function setUserSession($user)
    {
        $session = password_hash(JWT_KEY . $user['id'] . $user['time'], CRYPT_BLOWFISH);
        $sql = <<<sql
update users set user_session=? where user_id=?;
sql;
        $mysql = new MySQL();
        $mysql->prepare($sql, ['si', $session, $user['id']]);
    }
}