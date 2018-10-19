<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 11/10/18
 * Time: 03:49 AM
 */

class complaints
{
    function publish()
    {
        $message = isset_get($_REQUEST['mensaje']);
        $topic_id = isset_get($_REQUEST['tema']['id']);
        $user_id = isset_get($_REQUEST['usuario']['id']);

        $sql = <<<sql
insert into complaints(topic_id, user_id, complaint_message) VALUES ('$topic_id','$user_id','$message');
sql;

        db_query($sql);
        $id = db_last_id();
        if (!$id) {
            set_error('No se insertó la queja.', 500);
        }

        return compact('id');
    }

    function fetch()
    {
        $hashtag = isset_get($_REQUEST['hashtag']);
        $filters = isset_get($_REQUEST['filters']);
        $topics = join(',', isset_get($filters['topics'], array()));

        $sql = <<<sql
select complaint_id id, topic_name topic, user_username username, complaint_message message, complaint_date date
from complaints c
       left join topics t on t.topic_id = c.topic_id
       inner join users u on u.user_id = c.user_id
where 
complaint_message like '%$hashtag%'
and if('$topics'='',true, c.topic_id IN ('$topics'))
AND complaint_status = true
order by date asc;
sql;

        $complaints = db_all_results($sql);
        return compact('complaints');
    }
}