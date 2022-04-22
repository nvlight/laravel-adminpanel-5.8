<?php

namespace App\Repositories\Admin;

use App\Repositories\CoreRepository;
use App\Models\Admin\Product as Model;

class ProductRepository extends CoreRepository
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getModelClass()
    {
        return Model::class;
    }

    public function getLastProducts($perPage){
        $get = $this->startConditions()
            ->orderBy('id', 'desc')
            ->limit($perPage)
            ->paginate($perPage)
            ;
        return $get;
    }

    public function getAllProducts($perpage){
         $get_all = $this->startConditions()
             ->join('categories','categories.id','=','products.category_id')
             ->select('products.*','categories.title AS cat')
             ->toBase()
             ->orderBy(\DB::raw('LENGTH(products.title)', 'products.title'))
             ->limit($perpage)
             ->paginate($perpage)
             ;
         return $get_all;
    }

    public function getCountProducts(){
        $count = $this->startConditions()
            ->count();
        return $count;
    }
}
