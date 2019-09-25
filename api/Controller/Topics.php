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
        $Topics = new \Model\Topics();
        $topics = $Topics->selectTopics();
        return compact('topics');
    }
}