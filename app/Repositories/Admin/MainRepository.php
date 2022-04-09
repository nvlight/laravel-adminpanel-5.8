<?php


namespace App\Repositories\Admin;


use App\Repositories\CoreRepository;
use Illuminate\Database\Eloquent\Model;

class MainRepository extends CoreRepository
{

    protected function getModelClass()
    {
        return Model::class;
    }

//    public static function getCountOrders(){
//        $count = \DB::table('orders')
//            ->where('status', '0')
//            ->get()
//            ->count();
//        return $count;
//    }
//
//    public static function getCountUsers(){
//        $count = \DB::table('users')
//            ->get()
//            ->count();
//        return $count;
//    }
//
//    public static function getCountProducts(){
//        $count = \DB::table('products')
//            ->get()
//            ->count();
//        return $count;
//    }
//
//    public static function getCountCategories(){
//        $count = \DB::table('categories')
//            ->get()
//            ->count();
//        return $count;
//    }

    public static function getCountOrders(){
        return self::getTableCount('orders','1');
    }

    public static function getCountUsers(){
        return self::getTableCount('users');
    }

    public static function getCountProducts(){
        return self::getTableCount('products');
    }

    public static function getCountCategories(){
        return self::getTableCount('categories');
    }

    public static function getTableCount($table, $where=''){
        $count = \DB::table($table);
        if ($where) {
            $count = $count->where('status', '0');
        }
        $count = $count
            ->get()
            ->count();
        return $count;
    }
}