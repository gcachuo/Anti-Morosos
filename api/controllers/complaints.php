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
        $payer = isset_get($_REQUEST['moroso']);
        $user_id = isset_get($_REQUEST['usuario']['id']);

        $sql = <<<sql
select payer_id from payers where payer_name='$payer';
sql;
        $payer_id = db_result($sql)['payer_id'];

        if (!$payer_id) {
            $sql = <<<sql
insert into payers(payer_name) value ('$payer');
sql;
            db_query($sql);
            $payer_id = db_last_id();
        }

        $sql = <<<sql
insert into complaints(payer_id, user_id, complaint_message) VALUES ('$payer_id','$user_id','$message');
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

        $sql = <<<sql
select complaint_id id, payer_name payer, user_username username, complaint_message message, complaint_date date
from complaints c
       inner join payers p on p.payer_id = c.payer_id
       inner join users u on u.user_id = c.user_id
where 
complaint_message like '%$hashtag%'
AND complaint_status = true
order by date asc;
sql;

        $complaints = db_all_results($sql);
        return compact('complaints');
    }
}