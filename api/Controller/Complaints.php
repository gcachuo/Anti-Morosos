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
        $Complaints = new \Model\Complaints();
        $results = $Complaints->selectComplaintsWithHashtag();

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
        $complaint_id = System::isset_get($_POST['id']);
        $complaint_deleted = System::isset_get($_POST['razon'], 'null');
        $user_id = System::isset_get($_POST['usuario']['id']);

        System::check_value_empty(['id' => $complaint_id, 'razon' => $complaint_deleted, 'usuario' => $user_id], ['id', 'usuario'], 'Llene todos los datos');

        $Complaints = new \Model\Complaints();
        $Complaints->deleteComplaint($user_id, $complaint_id, $complaint_deleted);
        return [];
    }

    function edit()
    {
        $complaint_id = System::isset_get($_POST['id']);
        $complaint_message = System::isset_get($_POST['mensaje']);
        $user_id = System::isset_get($_POST['usuario']['id']);

        System::check_value_empty(['id' => $complaint_id, 'mensaje' => $complaint_message, 'usuario' => $user_id], ['id', 'mensaje', 'usuario'], 'Llene todos los datos.');

        $Complaints = new \Model\Complaints();
        $Complaints->insertHistory($user_id, $complaint_id);

        $Complaints->updateComplaint($user_id, $complaint_id, $complaint_message);
        return [];
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