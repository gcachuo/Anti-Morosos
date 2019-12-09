<?php


namespace Model;

use HTTPStatusCodes;
use JsonResponse;
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
            new TableColumn('user_type', ColumnTypes::INTEGER, 11),
        ], <<<sql
create unique index users_user_email_uindex on users (user_email);
create unique index users_user_referral_uindex on users (user_referral);
create unique index users_user_username_uindex on users (user_username);
create index users_users_user_id_fk on users (user_referrer);
sql
        );

        new Complaints();
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

    public function setReferralCode()
    {
        $mysql = new MySQL();
        do {
            $user_referral = str_pad(rand(0, 9999), 4, '0');
            $sql = <<<sql
select count(1) count from users where user_referral=?;
sql;
        } while ($mysql->fetch_single($mysql->prepare($sql, ['s', $user_referral]))['count']);
        return $user_referral;
    }

    public function userExists($user_username)
    {
        $sql = <<<sql
select count(1) count from users where user_username=?;
sql;
        $mysql = new MySQL();
        return (bool)$mysql->fetch_single($mysql->prepare($sql, ['s', $user_username]))['count'];
    }

    public function getUserReferrer($user_referrer)
    {
        $user_validation = 0;
        if ($user_referrer !== 'null') {
            $sql = <<<sql
select user_id referrer from users where user_referral=?;
sql;
            $mysql = new MySQL();
            if (!$mysql->fetch_single($mysql->prepare($sql, ['s', $user_referrer]))['referrer']) {
                JsonResponse::sendResponse(['message' => 'La referencia es inválida.']);
            }

            $user_validation = 1;
        }
        return $user_validation;
    }

    public function insertUser($user_email, $user_username, $user_password, $user_name, $user_lastname_1, $user_lastname_2, $user_company, $user_business_name, $user_business_position, $user_business_phone, $user_whatsapp, $user_referrer, $user_referral, $user_validation)
    {
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
        $mysql = new MySQL();
        $mysql->query($sql);
        $id = $mysql->insertID();

        if (!$id) {
            JsonResponse::sendResponse(['message' => 'La referencia es inválida.', 'error' => $mysql->last_error()], HTTPStatusCodes::InternalServerError);
        }
        return $id;
    }

    public function getPasswordHashFromId($user_id)
    {
        $sql = <<<sql
select user_password password from users where user_id=?
sql;

        $mysql = new MySQL();
        $mysqli_result = $mysql->prepare($sql, ['i', $user_id]);
        return System::isset_get($mysql->fetch_single($mysqli_result)['password']);
    }

    public function updatePassword($user_id, $user_password)
    {
        $sql = <<<sql
update users set user_password=? where user_id=?;
sql;

        $mysql = new MySQL();
        $mysql->prepare($sql, ['si', $user_password, $user_id]);
    }

    public function selectComplaintsFeed()
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
        $mysql = new MySQL();
        return $mysql->fetch_all($mysql->query($sql));
    }

    public function getSessionToken($user_id)
    {
        $sql = <<<sql
select user_session token from users where user_id=?
sql;

        $mysql = new MySQL();
        return $mysql->fetch_single($mysql->prepare($sql, ['i', $user_id]))['token'];
    }

    public function getUserType($user_id)
    {
        $sql = <<<sql
select user_type type from users where user_id=?;
sql;
        $mysql = new MySQL();
        return System::isset_get($mysql->fetch_single($mysql->prepare($sql, ['i', $user_id]))['type']);
    }

    public function selectUsersWithValidation($user_id)
    {
        $sql = <<<sql
select user_id id,user_name name, user_validation validation from users where user_status=true and user_id<>?;
sql;
        $mysql = new MySQL();
        return $mysql->fetch_all($mysql->prepare($sql, ['i', $user_id]));
    }

    public function updateUserValidation($user_id, $user_validation)
    {
        $sql = <<<sql
update users set user_validation=? where user_id=?
sql;
        $mysql = new MySQL();
        $mysql->prepare($sql, ['ii', $user_validation, $user_id]);
    }
}