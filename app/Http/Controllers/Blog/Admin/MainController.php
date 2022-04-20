<?php

namespace App\Http\Controllers\Blog\Admin;

use App\Repositories\Admin\MainRepository;
use App\Repositories\Admin\OrderRepository;
use App\Repositories\Admin\ProductRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use MetaTag;

class MainController extends AdminBaseController
{
    private $orderRepository;
    private $productRepository;

    public function __construct()
    {
        parent::__construct();
        $this->orderRepository = app(OrderRepository::class);
        $this->productRepository= app(ProductRepository::class);
    }

    public function index()
    {
        $countOrders = MainRepository::getCountOrders();
        $countUsers = MainRepository::getCountUsers();
        $countProducts = MainRepository::getCountProducts();
        $countCategories = MainRepository::getCountCategories();

        $perPage = 5;
        $lastOrders = $this->orderRepository->getAllOrders($perPage);
        $lastProducts = $this->productRepository->getLastProducts($perPage);
        //dump($lastOrders);
        //dump($lastProducts);

        MetaTag::setTags(['title' => 'Admin Panel']);
        return view('blog.admin.main.index',
            compact('countOrders', 'countUsers','countProducts',
                'countCategories', 'lastOrders','lastProducts'));
    }
}
