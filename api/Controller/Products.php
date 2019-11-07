<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 20/10/18
 * Time: 11:30 PM
 */

namespace Controller;

use Controller;

class Products extends Controller
{
    public function __construct()
    {
        parent::__construct([
            'POST' => [
                'fetch' => 'fetch'
            ]
        ]);
    }

    function fetch()
    {
        $Products = new \Model\Products();
        $products = $Products->selectProducts();
        return compact('products');
    }
}