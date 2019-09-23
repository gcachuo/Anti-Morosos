<?php
/**
 * Created by PhpStorm.
 * User: memo
 * Date: 19/10/18
 * Time: 01:18 AM
 */

namespace Controller;

class Topics
{
    function fetch()
    {
        $sql = <<<sql
select topic_id id, topic_name name from topics
where topic_status=true;
sql;
        $topics = db_all_results($sql);
        return compact('topics');
    }
}