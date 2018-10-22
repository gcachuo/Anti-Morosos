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
select complaint_id id, topic_name topic, user_username username, group_concat(product_name) products ,complaint_message message, complaint_date date
from complaints c
       left join topics t on t.topic_id = c.topic_id
       inner join users u on u.user_id = c.user_id
       left join users_products up on up.user_id=u.user_id
       left join products p on p.product_id = up.product_id
where 
complaint_message like '%#$hashtag%'
and if('$topics'='',true, c.topic_id IN ('$topics'))
AND complaint_status = true
group by complaint_id
order by complaint_date asc;
sql;

        $complaints = db_all_results($sql);
        return compact('complaints');
    }

    function trending()
    {
        $trending = [];
        $sql = <<<sql
select lower(complaint_message) from complaints where complaint_message like '%#%';
sql;

        $results = db_all_results($sql, MYSQLI_NUM);
        if ($results) {
            $results = join(' ', array_merge(...$results));
            preg_match_all('/#.+?\b/m', $results, $matches, PREG_SET_ORDER, 0);
            $trending = array_merge(...$matches);
            $trending = array_count_values($trending);
            arsort($trending);
            $trending = array_splice($trending, 0, 5);
        }
        return compact('trending');
    }
}