<?php

namespace App\Http\Controllers\Blog\Admin;

use App\Http\Requests\AdminProductsCreateRequest;
use App\Models\Admin\Category;
use App\Models\Admin\Product;
use App\Repositories\Admin\ProductRepository;
use App\SBlog\Core\BlogApp;
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
        $product = new Product();
        $categories = Category::
                        where('parent_id','0')
                        ->get();

        MetaTag::setTags(['title' => 'Создание нового продукта']);
        return view('blog.admin.product.create', [
            'categories' => $categories,
            'delimiter' => '-',
            'product' => $product,
        ]);
    }

    /**
     *
     * @param AdminProductsCreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AdminProductsCreateRequest $request)
    {
        $data = $request->all();
        $product = (new Product())->make($data);

        //dump($product);
        $product->status = $request->status ? '1' : '0';
        $product->hit    = $request->hit ? '1' : '0';
        $product->category_id = $request->parent_id ?? '0';
        $product->brand_id = 1; // todo - just fix it for now, rework after!
        //dump($product);
        $save = $product->save();
        if ($save){
            //dump(\Session::all());
            //dump(\Session::get('gallery'));
            $id = $product->id;
            $this->productRepository->getImg($product);
            $this->productRepository->editFilter($id, $data);
            $this->productRepository->editRelatedProduct($id, $data);
            $this->productRepository->saveGallery($id);

            return redirect()
                ->route('blog.admin.products.edit', [$product->id])
                ->with(['success' => 'Успешно сохранено']);
        }else{
            return back()
                ->withErrors(['img' => 'Ошибка сохранения'])
                ->withInput();
        }
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
    public function edit(Product $product)
    {
        //$product = $this->productRepository->getInfoProduct($id);
        $id = $product->id;
        $categories = Category::
            where('parent_id','0')
            ->get();

        BlogApp::get_instance()->setProperty('parent_id', $product->category_id);
        //dump(BlogApp::get_instance()->getProperty('parent_id'));

        $filter = $this->productRepository->getFiltersProduct($id);
        $related = $this->productRepository->getRelatedProduct($id);
        $images = $this->productRepository->getGallery($id);
        //dump($categories);
        //dump($images);
        //dump($filter);
        //dump($related->toArray());
        //die;

        MetaTag::setTags(['title' => 'Редактирование продукта #' . $id]);
        return view('blog.admin.product.edit', [
            'categories' => $categories,
            'delimiter' => '-',
            'product' => $product,
            'filter' => $filter,
            'related' => $related,
            'images' => $images,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdminProductsCreateRequest $request, Product $product)
    {
        $data = $request->all();
        //dump($data);
        //dump($product->toArray());
        $product->fill($data);
        $product->category_id = $request->parent_id ?? 0;
        $product->hit =    $request->hit    ? '1' : '0';
        $product->status = $request->status ? '1' : '0';
        //dump($product->toArray());
        $this->productRepository->getImg($product);
        $save = $product->save();
        if ($save){
            //$this->productRepository->getImg($product);
            $this->productRepository->editFilter($product->id, $data);
            $this->productRepository->editRelatedProduct($product->id, $data);
            $this->productRepository->saveGallery($product->id);

            return redirect()
                ->route('blog.admin.products.edit', [$product->id])
                ->with(['success' => 'Успешно обновлено']);
        }else{
            return back()
                ->withErrors()
                ->withInput();
        }
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

    protected function fileUploadValidator($request){
        $validator = \Validator::make($request->all(),
            [
                //'file' => 'image|max:5000',
                //'file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5000',
                'file' => 'mimes:jpeg,png,jpg,gif,svg|max:5000',
            ],
            [
                'file.image' => 'Файл должен быть картинкой (jpeg, png, gif, svg)',
                'file.mimes' => 'Ожидается картинка (jpeg,png,jpg,gif,svg)',
                'file.max' => 'Максимальный размер картинки - 5 Мб!',
            ]);
        return $validator;
    }
    protected function fileUploadValidate($validator){
        if ($validator->fails()){
            return [    'fail' => true,
                'errors' => $validator->errors(),];
        }else{
            return [ 'fail' => false ];
        }
    }

    public function ajaxImage(Request $request){
        if ($request->isMethod('get')){
            return view('blog.admin.product.include.image_single_edit');
        }else{
            $validator = $this->fileUploadValidator($request);
            if ($this->fileUploadValidate($validator)['fail']){
                //return $validator;
                return $this->fileUploadValidate($validator);
            }

            $extension = $request->file('file')->getClientOriginalExtension();
            $dir = 'uploads/single/';
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $request->file('file')->move($dir, $filename);
            $vmax = BlogApp::get_instance()->getProperty('img_width');
            $hmax = BlogApp::get_instance()->getProperty('img_height');

            $this->productRepository->resizeImg($dir . $filename, $dir . $filename, $extension, $vmax, $hmax);
            \Session::put('single', $filename);
            \Session::save();
            return $filename;
        }
    }

    /**
     * Delete single image
     *
     * @param $filename
     * @return void
     */
    public function deleteImage($filename){
        $result = [];
        try {
            $result['success'] = 1;
            $result['message'] = 'success deleted! file: ' . $filename;
            \File::delete('uploads/single/'.$filename);
        }catch (\Exception $e){
            $result['success'] = 0;
            $result['message'] = 'error with delete file: ' . $filename;
            $result['error'] = $e->getCode() . ' - ' . $e->getMessage();
        }
        die(json_encode($result));
//        $allFiles = \File::allFiles('uploads/single/');
//        dump($allFiles);
//        dump($allFiles[0]->getFilename());\File::exists()
    }

    /**
     * Delete img from gallery
     *
     * @param Request $request
     * @return void
     */
    public function deleteGallery(Request $request){
        $id = $request->post('id') ?? null;
        $src = $request->post('src') ?? null;

        if (!$id || !$src){
            $result = [
                'success' => 0,
                'message' => 'id or src is null'
            ];
            die(json_encode($result));
        }
        $result = [
            'id' => $id,
            'src' => $src,
        ];
        //die(json_encode($result));

        if (\DB::delete("DELETE from galleries WHERE product_id = ? AND img = ?", [$id, $src])){
            @unlink("uploads/gallery/{$src}");
            $result = [
                'success' => 1,
                'message' => 'Удалена запись в БД и сам файл!',
            ];
            die(json_encode($result));
        }
        $result = [
            'success' => 0,
            'message' => 'Что-то пошло не так при удалении',
        ];
        die(json_encode($result));
    }

    public function gallery(Request $request){
        $validator = $this->fileUploadValidator($request);
        if ($this->fileUploadValidate($validator)['fail']){
            //return $validator;
            return $this->fileUploadValidate($validator);
        }

        if ($request->has('upload')){
            $gallery_width = BlogApp::get_instance()->getProperty('gallery_width');
            $gallery_height = BlogApp::get_instance()->getProperty('gallery_height');
            $name = $request->post('name');
            $result = $this->productRepository->uploadGallery($name, $gallery_width, $gallery_height);
            if (array_key_exists('file', $result)){
                \Session::push('gallery', $result['file']);
                \Session::save();
                $result['dump'] = \Session::get('gallery');
            }
            die(json_encode($result));
        }
    }

    /**
     * Return product status = 1
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function returnStatus($id){
        if ($id){
            $st = $this->productRepository->returnStatusOne($id);
            if ($st){
                return redirect()
                    ->route('blog.admin.products.index')
                    ->with(['success' => 'Успешно сохранен статус!']);
            }else{
                return back()
                    ->withErrors(['msg' => 'Ошибка сохранения статуса'])
                    ->withInput();
            }
        }
    }

    /**
     * Set product status = 0
     * @param $id
     * @return void
     */
    public function deleteStatus($id){
        if ($id){
            $st = $this->productRepository->deleteStatusOne($id);
            if ($st){
                return redirect()
                    ->route('blog.admin.products.index')
                    ->with(['success' => 'Успешно сохранен статус!']);
            }else{
                return back()
                    ->withErrors(['msg' => 'Ошибка сохранения статуса'])
                    ->withInput();
            }
        }
    }

    /**
     * Set product status = $status [0,1]
     * @param $id
     * @return void
     */
    public function changeStatus(Product $product, $status){
        $clearStatus = $status ? '1' : '0';
        $result = $this->productRepository->changeStatus($product->id, $clearStatus);
        if ($result){
            return redirect()
                ->route('blog.admin.products.index')
                ->with(['success' => 'Успешно сохранен статус!']);
        }else{
            return back()
                ->withErrors(['msg' => 'Ошибка сохранения статуса'])
                ->withInput();
        }
    }

    public function deleteProduct(Product $product){
        $gallery = $this->productRepository->deleteImgGalleryFromPath($product);
        $db = $this->productRepository->deleteFromDB($product);

        if ($db){
            return redirect()
                ->route('blog.admin.products.index')
                ->with(['success' => 'Успешно удалено!']);
        }else{
            return back()
                ->withErrors(['msg' => 'Ошибка удаления!'])
                ->withInput();
        }
    }
}
