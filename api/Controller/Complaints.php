<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 11/10/18
 * Time: 03:49 AM
 */

namespace Controller;

use System;

class Complaints
{
    function publish()
    {
        $message = System::isset_get($_POST['mensaje']);
        $topic_id = System::isset_get($_POST['tema']['id']);
        $user_id = System::isset_get($_POST['usuario']['id']);

        System::check_value_empty(['mensaje' => $message, 'tema' => $topic_id, 'usuario' => $user_id], ['mensaje', 'tema', 'usuario'], 'Llene todos los campos');

        $Complaints = new \Model\Complaints();
        $id = $Complaints->insertComplaint($user_id, $message, $topic_id);

        return compact('id');
    }

    function fetch()
    {
        $user_id = System::isset_get($_POST['usuario']['id']);
        $hashtag = System::isset_get($_POST['hashtag']);
        $filters = [
            'topics' => System::isset_get($_POST['filters']['topics']),
            'u' => System::isset_get($_POST['filters']['u']),
        ];

        System::check_value_empty(['usuario' => $user_id, 'hashtag' => $hashtag, 'filters' => $filters], ['usuario', 'filters'], 'Llene todos los campos');

        $topics = join(',', System::isset_get($filters['topics'], []));
        $filters['user'] = System::isset_get($filters['u']);

        $Users = new \Model\Users();
        $type = $Users->getUserType($user_id);

        $Complaints = new \Model\Complaints();
        $complaints = $Complaints->selectComplaints($user_id, $type, $hashtag, $topics, $filters);

        return compact('complaints');
    }

    function trending()
    {
        $trending = [];
        $sql = <<<sql
select lower(complaint_message) from complaints where complaint_message like '%#%' and complaint_status=true;
sql;

        $results = db_all_results($sql, MYSQLI_NUM);
        if ($results) {
            $results = join(' ', array_merge(...$results));
            preg_match_all('/#.+?\b/m', $results, $matches, PREG_SET_ORDER, 0);
            $trending = array_merge(...$matches);
            $trending = array_count_values($trending);
            arsort($trending);
            $trending = array_splice($trending, 0/*, 5*/);
        }
        return compact('trending');
    }

    function delete()
    {
        $complaint_id = isset_get($_REQUEST['id']);
        $complaint_deleted = isset_get($_REQUEST['razon'], 'null');
        $user_id = isset_get($_REQUEST['usuario']['id']);

        $sql = <<<sql
update complaints c
inner join users u on u.user_id='$user_id'
 set complaint_status=false, complaint_deleted='$complaint_deleted'
where complaint_id='$complaint_id' and (c.user_id='$user_id' or u.user_type=0);
sql;
        db_query($sql);
    }

    function edit()
    {
        $complaint_id = isset_get($_REQUEST['id']);
        $complaint_message = isset_get($_REQUEST['mensaje']);
        $user_id = isset_get($_REQUEST['usuario']['id']);

        $sql = <<<sql
insert into complaints_history(complaint_id, complaint_history_message,user_id)
select complaint_id,complaint_message,'$user_id' from complaints
where complaint_id='$complaint_id';
sql;
        db_query($sql);

        $sql = <<<sql
update complaints c
inner join users u on u.user_id='$user_id'
 set complaint_message='$complaint_message' 
where complaint_id='$complaint_id' and (c.user_id='$user_id' or u.user_type=0);
sql;
        db_query($sql);
    }

    function markasread()
    {
        $complaint_id = isset_get($_REQUEST['id']);
        $user_id = isset_get($_REQUEST['usuario']['id']);

        $sql = <<<sql
replace into complaints_users(complaint_id, user_id, complaint_user_read) VALUES ('$complaint_id','$user_id',true);
sql;
        db_query($sql);
    }
}