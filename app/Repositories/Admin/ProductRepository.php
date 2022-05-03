<?php

namespace App\Repositories\Admin;

use App\Models\Admin\Product;
use App\Repositories\CoreRepository;
use App\Models\Admin\Product as Model;
use Illuminate\Support\Facades\DB;

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

    public function getProducts($q){
        $products = \DB::table('products')
            ->select('id', 'title')
            ->where('title','LIKE', ["%{$q}%"])
            ->limit(8)
            ->get();
        return $products;
    }

    /**  resize Single Image*/
    public function resizeImg($target, $dest, $ext, $wmax, $hmax)
    {
        self::resize($target, $target, $wmax, $hmax, $ext);
    }

    /**  Resize Images for My needs */
    public static function resize($target, $dest, $wmax, $hmax, $ext)
    {
        list($w_orig, $h_orig) = getimagesize($target);
        $ratio = $w_orig / $h_orig;

        if (($wmax / $hmax) > $ratio) {
            $wmax = $hmax * $ratio;
        } else {
            $hmax = $wmax / $ratio;
        }

        $img = "";
        // imagecreatefromjpeg | imagecreatefromgif | imagecreatefrompng
        switch ($ext) {
            case("gif"):
                $img = imagecreatefromgif($target);
                break;
            case("png"):
                $img = imagecreatefrompng($target);
                break;
            default:
                $img = imagecreatefromjpeg($target);
        }
        $newImg = imagecreatetruecolor($wmax, $hmax);
        if ($ext == "png") {
            imagesavealpha($newImg, true);
            $transPng = imagecolorallocatealpha($newImg, 0, 0, 0, 127);
            imagefill($newImg, 0, 0, $transPng);
        }
        imagecopyresampled($newImg, $img, 0, 0, 0, 0, $wmax, $hmax, $w_orig,
            $h_orig); // копируем и ресайзим изображение
        switch ($ext) {
            case("gif"):
                imagegif($newImg, $dest);
                break;
            case("png"):
                imagepng($newImg, $dest);
                break;
            default:
                imagejpeg($newImg, $dest);
        }
        imagedestroy($newImg);
    }

    /**
     * Upload Gallery Ajax
     * @param $name
     * @param $wmax
     * @param $hmax
     * @return void
     */
    public function uploadGallery($name, $wmax, $hmax){
        $uploadDir = 'uploads/gallery/';
        $extension = strtolower(
            preg_replace("#.+\.([a-z]+)$#i", '$1', $_FILES[$name]['name'])
        );
        $newName = sha1(time()) . ".{$extension}";
        $uploadFile = $uploadDir . $newName;

        $result = [];
        if (@move_uploaded_file($_FILES[$name]['tmp_name'], $uploadFile)){
            self::resize($uploadFile, $uploadFile, $wmax, $hmax, $extension);
            $result['file'] = $newName;
            //\Session::push('wiw', $result['file']);
            //die(json_encode(['wiw' => 'yes!']));
        }
        $result['success'] = 1;
        return $result;
    }

    /**
     * Ger img for new Product
     * @param $product
     * @return void
     */
    public function getImg(Product $product){
        clearstatcache();
        if ( \Session::has('single')){
            $product->img = \Session::get('single');
            $product->save();
            \Session::forget('single');
            //dump('wiw?');
            //dump($product->toArray());
            //die;
            return;
        }
        if (!\Session::get('single') && !is_file(WWW . '/uploads/single/') . $product->img ){
            $product->img = null;
            $product->save();
            return;
        }
    }

    /**
     * Edit filter
     *
     * @param $id
     * @param $data
     * @return void
     */
    public function editFilter($id, $data){
        $filter = DB::table('attribute_products')
            ->where('product_id',$id)
            ->pluck('attr_id')
            ->toArray();

        /** если убрали фильтры на клиенте */
        if(isset($data['attrs']) && !count($data['attrs']) && count($filter)){
            DB::table('attribute_products')
                ->where('product_id', $id)
                ->delete();
            return;
        }
        //dump($filter);
        //dump($id);
        //dump($data);

        /** если добавили фильтры */
        if(isset($data['attrs']) && !count($filter) && count($data['attrs'])){
            $sql_part = '';
            foreach($data['attrs'] as $v){
                $sql_part .= "({$v}, {$id}),";
            }
            $sql_part = rtrim($sql_part, ',');
            //dump($sql_part);
            //die;
            //echo 'wiw' . "<br>";
            DB::insert("insert into attribute_products (attr_id, product_id) VALUES {$sql_part}");
            return;
        }

        /** если меняем фильтры */
        if (isset($data['attrs']) && count($data['attrs']) && array_diff($filter, $data['attrs'])){
            //dump($result);
            // #1 delete
            DB::table('attribute_products')
                ->where('product_id', $id)
                ->delete();

            // #2 insert
            $sql_part = '';
            foreach($data['attrs'] as $v){
                $sql_part .= "({$v}, {$id}),";
            }
            $sql_part = rtrim($sql_part, ',');
            DB::insert("insert into attribute_products (attr_id, product_id) VALUES {$sql_part}");
            return;
        }

    }

    /**
     * Edit related products
     * @param $id
     * @param $data
     * @return void
     */
    public function editRelatedProduct($id, $data){
        $related_products = DB::table('related_products')
            ->select('related_id')
            ->where('product_id', $id)
            ->pluck('related_id')
            ->toArray();
        //dump($related_products);

        /** Если убрали связанные товары */
        if (!empty($related_products) && empty($data['related'])){
            DB::table('related_products')
                ->where('product_id', $id)
                ->delete();
            return;
        }

        /** Если добавили связанные товары */
        if (empty($related_products) && !empty($data['related'])){
            foreach($data['related'] as $v){
                $v = (int)$v;
                $sql_part = '';
                $sql_part .= "({$id},{$v}),";
                $sql_part = rtrim($sql_part, ',');
                DB::insert("insert into related_products (product_id, related_id) VALUES $sql_part");
                return;
            }
        }

        /** Если поменяли связанные товары */
        if (!empty($data['related']) && array_diff($related_products, $data['related'])){
            // #1 delete
            DB::table('related_products')
                ->where('product_id', $id)
                ->delete();
            // #2 insert
            foreach($data['related'] as $v){
                $v = (int)$v;
                $sql_part = '';
                $sql_part .= "({$id},{$v}),";
                $sql_part = rtrim($sql_part, ',');
                DB::insert("insert into related_products (product_id, related_id) VALUES $sql_part");
                return;
            }

            return;
        }
    }

    /**
     * Save Gallery
     * @param $id
     * @return void
     */
    public function saveGallery($id){
        if (\Session::has('gallery') && count(\Session::get('gallery'))){
            $sql_part = "";
            foreach(\Session::get('gallery') as $v){
                $sql_part .= "('{$v}',{$id}),";
            }
            $sql_part = rtrim($sql_part, ',');
            DB::insert("insert into galleries (img, product_id) VALUES $sql_part");
            \Session::forget('gallery');
        }
    }
}
