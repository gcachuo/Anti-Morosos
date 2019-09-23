<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 20/10/18
 * Time: 11:30 PM
 */

namespace Controller;

class Products
{
    function fetch()
    {
        $sql = <<<sql
select product_id id, product_name name from products
where product_status=true
order by product_name;
sql;
        $products = db_all_results($sql);
        return compact('products');
    }
}