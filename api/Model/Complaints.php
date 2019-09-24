<?php


namespace Model;


use HTTPStatusCodes;
use JsonResponse;

class Complaints
{
    public function __construct()
    {
        $mysql = new MySQL();
        $mysql->create_table('complaints', [
            new TableColumn('complaint_id', ColumnTypes::BIGINT, 20, true, null, true, true),
            new TableColumn('user_id', ColumnTypes::BIGINT, 20, true),
            new TableColumn('topic_id', ColumnTypes::BIGINT, 20),
            new TableColumn('complaint_message', ColumnTypes::VARCHAR, 255),
            new TableColumn('complaint_date', ColumnTypes::TIMESTAMP, 0, false, 'current_timestamp'),
            new TableColumn('complaint_deleted', ColumnTypes::VARCHAR, 255),
            new TableColumn('complaint_status', ColumnTypes::BIT, 1, false, "b'1'")
        ]);
    }

    /**
     * @param $user_id
     * @param $complaint_message
     * @param $topic_id
     */
    public function insertComplaint($user_id, $complaint_message, $topic_id)
    {
        $sql = <<<sql
insert into complaints(topic_id, user_id, complaint_message) VALUES (?,?,?);
sql;
        $mysql = new MySQL();
        $mysql->prepare($sql, ['iis', $topic_id, $user_id, $complaint_message]);
        $id = $mysql->insertID();

        if (!$id) {
            JsonResponse::sendResponse(['message' => 'No se insertó la queja.'], HTTPStatusCodes::InternalServerError);
        }
        return $id;
    }
}