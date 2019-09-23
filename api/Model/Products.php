<?php

namespace Model;

use System;

class Products
{
    public function __construct()
    {
        $mysql = new MySQL();
        $mysql->create_table('products', [
            new TableColumn('product_id', ColumnTypes::BIGINT, 20, true, null, true, true),
            new TableColumn('product_name', ColumnTypes::VARCHAR, 100, true),
            new TableColumn('product_status', ColumnTypes::BIT, 1, false, "b'1'")
        ]);
        $mysql->create_table('users_products', [
            new TableColumn('user_product_id', ColumnTypes::BIGINT, 20, true, null, true, true),
            new TableColumn('user_id', ColumnTypes::BIGINT, 20),
            new TableColumn('product_id', ColumnTypes::BIGINT, 20)
        ]);
    }

    public function insertProducts($products, $user_id)
    {
        $mysql = new MySQL();
        foreach (System::isset_get($products, []) as $product) {
            if (!is_numeric($product)) {
                $sql = <<<sql
replace into products(product_name) values ('$product');
sql;
                $mysql->query($sql);
                $product = $mysql->insertID();
            }
            $sql = <<<sql
replace into users_products(product_id, user_id) VALUES ('$product','$user_id');
sql;
            $mysql->query($sql);
        }
    }

}