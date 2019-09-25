<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 20/10/18
 * Time: 11:30 PM
 */

namespace Controller;

use System;

class Products
{
    function fetch()
    {
        System::allowed_methods(['GET']);

        $Products = new \Model\Products();
        $products = $Products->selectProducts();
        return compact('products');
    }
}