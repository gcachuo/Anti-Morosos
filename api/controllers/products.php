<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 20/10/18
 * Time: 11:30 PM
 */

class products
{
    function fetch()
    {
        $sql = <<<sql
select product_id id, product_name name from products
where product_status=true;
sql;
        $products = db_all_results($sql);
        return compact('products');
    }
}