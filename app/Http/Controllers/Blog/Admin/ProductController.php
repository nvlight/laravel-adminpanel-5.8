<?php

namespace App\Http\Controllers\Blog\Admin;

use App\Models\Admin\Category;
use App\Models\Admin\Product;
use App\Repositories\Admin\ProductRepository;
use Illuminate\Http\Request;
use MetaTag;

class ProductController extends AdminBaseController
{
    private $productRepository;

    public function __construct()
    {
        parent::__construct();
        $this->productRepository = app(ProductRepository::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perpage = 10;
        $getAllProducts = $this->productRepository->getAllProducts($perpage);
        $count = $this->productRepository->getCountProducts();
        //dump($getAllProducts);
        //dump($count);
        //die;

        MetaTag::setTags(['title' => 'Список продуктов']);
        return view('blog.admin.product.index',
            compact('getAllProducts', 'count'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $item = new Product();
        $categories = Category::
                        where('parent_id','0')
                        ->get();

        MetaTag::setTags(['title' => 'Создание нового продукта']);
        return view('blog.admin.product.create', [
            'categories' => $categories,
            'delimiter' => '-',
            'item' => $item,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        dump($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function related(Request $request){
        $q = isset($request->q) ? htmlspecialchars(trim($request->q)):'';
        $data['items'] = [];
        $products = $this->productRepository->getProducts($q);
        if ($products){
            $i=0;
            foreach($products as $k => $v){
                $data['items'][$i]['id'] = $v->id;
                $data['items'][$i]['text'] = $v->title;

                $i++;
            }
        }
        die(json_encode($data));
    }
}
