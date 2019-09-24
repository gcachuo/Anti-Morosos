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
        $mysql->create_table('complaints_users', [
            new TableColumn('complaint_user_id', ColumnTypes::BIGINT, 20, true, null, true, true),
            new TableColumn('complaint_user_date', ColumnTypes::TIMESTAMP, 0, false, 'current_timestamp'),
            new TableColumn('complaint_id', ColumnTypes::BIGINT, 20, true),
            new TableColumn('user_id', ColumnTypes::BIGINT, 20, true),
            new TableColumn('complaint_user_read', ColumnTypes::BIT, false, "b'1'")
        ]);

        new Topics();
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

    public function selectComplaints($user_id, $user_type, $hashtag, $topics, array $filters)
    {
        $sql = <<<sql
select c.complaint_id                                                  id,
       topic_name                                                      topic,
       user_username                                                   username,
       group_concat(product_name order by p.product_id SEPARATOR ', ') products,
       complaint_message                                               message,
       group_concat(complaint_date)                                                  date,
       group_concat(coalesce(complaint_user_read, 0))                                messageRead,
       ('$user_id' = c.user_id or '$user_type'=0)                                     actions
from complaints c
       left join topics t on t.topic_id = c.topic_id
       inner join users u on u.user_id = c.user_id
       left join users_products up on up.user_id = u.user_id
       left join products p on p.product_id = up.product_id
       left join complaints_users cu on cu.complaint_id = c.complaint_id and cu.user_id=?
where if('$hashtag' = '', true, complaint_message like '%#$hashtag%')
  and if('$topics' = '', true, c.topic_id IN ('$topics'))
  and if('$filters[user]' = '', true, u.user_username = ?)
  AND complaint_status = true
group by c.complaint_id
order by messageRead desc , date asc;
sql;
        $mysql = new MySQL();
        return $mysql->fetch_all($mysql->prepare($sql, ['is', $user_id, $filters['user']]));
    }

    public function selectComplaintsWithHashtag()
    {
        $sql = <<<sql
select lower(complaint_message) from complaints where complaint_message like '%#%' and complaint_status=true;
sql;

        $mysql = new MySQL();
        return $mysql->fetch_all($mysql->query($sql), false, MYSQLI_NUM);
    }

    public function deleteComplaint($user_id, $complaint_id, $complaint_deleted = null)
    {
        $sql = <<<sql
update complaints c
inner join users u on u.user_id=?
 set complaint_status=false, complaint_deleted=?
where complaint_id=? and (c.user_id=? or u.user_type=0);
sql;
        $mysql = new MySQL();
        $mysql->prepare($sql, ['isii', $user_id, $complaint_deleted, $complaint_id, $user_id]);
    }
}