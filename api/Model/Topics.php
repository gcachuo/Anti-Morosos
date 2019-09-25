<?php


namespace Model;


class Topics
{
    public function __construct()
    {
        $mysql = new MySQL();
        $mysql->create_table('topics', [
            new TableColumn('topic_id', ColumnTypes::BIGINT, 20, true, null, true, true),
            new TableColumn('topic_name', ColumnTypes::VARCHAR, 100, true),
            new TableColumn('topic_status', ColumnTypes::BIT, 1, false, "b'1'")
        ]);
    }

    public function selectTopics()
    {
        $sql = <<<sql
select topic_id id, topic_name name from topics
where topic_status=true;
sql;
        $mysql = new MySQL();
        return $mysql->fetch_all($mysql->query($sql));
    }
}