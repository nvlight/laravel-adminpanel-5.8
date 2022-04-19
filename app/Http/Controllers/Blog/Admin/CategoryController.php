<?php

namespace App\Http\Controllers\Blog\Admin;

use App\Http\Requests\BlogCategoryUpdateRequest;
use App\Models\Admin\Category;
use App\Repositories\Admin\CategoryRepository;
use Illuminate\Http\Request;
use MetaTag;

class CategoryController extends AdminBaseController
{
    private $categoryRepository;

    public function __construct()
    {
        parent::__construct();
        $this->categoryRepository= app(CategoryRepository::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $arrMenu = Category::all();
        $menu = $this->categoryRepository->buildMenu($arrMenu);

        //dump($menu);

        MetaTag::setTags(['title' => 'Список категорий']);
        $delimiter = '-';
        return view('blog.admin.category.index', compact('menu', 'delimiter'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $item = new Category();
        $categoryList = $this->categoryRepository->getComboBoxCategories();

        //dump();

        MetaTag::setTags(['title' => 'Create new category']);

        // Category::where('parent_id',0)->get()
        //'categories' => Category::with('children')

        return view('blog.admin.category.create',[
            'categories' => Category::
                where('parent_id',0)
                ->get(),
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
    public function store(BlogCategoryUpdateRequest $request)
    {
        $notUnigue = $this->categoryRepository->checkUnigueName($request->title, $request->parent_id);
        if ($notUnigue){
            return back()
                ->withErrors(['msg' => 'Не может быть в одной и той же категории двух одинаковых. Выберите другую категорию.'])
                ->withInput();
        }
        $data = $request->input();
        //dump($data);
        $item = new Category();
        $item->fill($data)->save();
        if ($item){
            return redirect()
                ->route('blog.admin.categories.create', [$item->id])
                ->with(['success' => 'Success store']);
        }
        return back()
            ->withErrors(['msg' => 'Store error'])
            ->withInput();
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
    public function edit($id, CategoryRepository $categoryRepository)
    {
        //dump("Редактирование категории {$id}");
        $item = $this->categoryRepository->getId($id);

        MetaTag::setTags(['title' => "Редактирование категории {$id}"]);

        return view('blog.admin.category.edit',[
            'categories' => Category::
            where('parent_id',0)
                ->get(),
            'delimiter' => '-',
            'item' => $item,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BlogCategoryUpdateRequest $request, $id)
    {
        $item = $this->categoryRepository->getId($id);
        //dd($request->all());
        if (!($item)){
            return back()
                ->withErrors(['msg' => "Запись = [{$id}] не найдена"])
                ->withInput();
        }
        $data = $request->all();
        $result = $item->update($data);
        if ($result){
            return redirect()
                ->route('blog.admin.categories.edit', [$item->id])
                ->with(['success' => 'Успешно обновлено']);
        }
        return back()
            ->withErrors(['msg' => 'Ошибка обновления'])
            ->withInput();
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

    public function mydel(){
        $id = $this->categoryRepository->getRequestID();
        if (!($id)){
            return back()->withErrors(['msg' => 'id goes wrong!']);
        }

        $children = $this->categoryRepository->checkChildren($id);
        if ($children){
            return back()->withErrors(['msg' => 'Delete is disabled, category have children categories!']);
        }

        $parents = $this->categoryRepository->checkParentsProducts($id);
        if ($parents){
            return back()->withErrors(['msg' => 'Delete is disabled, category have children products!']);
        }

        $delete = $this->categoryRepository->deleteCategory($id);
        if (!$delete){
            return back()->withErrors(['msg' => 'Delete error!']);
        }

        return redirect()
            ->route('blog.admin.categories.index')
            ->with(['success' => "Record id [$id] is deleted!"]);
    }

}
