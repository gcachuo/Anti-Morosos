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
}