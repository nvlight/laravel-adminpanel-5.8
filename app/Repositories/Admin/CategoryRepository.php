<?php

namespace App\Repositories\Admin;

use App\Repositories\CoreRepository;
use App\Models\Admin\Category as Model;
use Menu as LavMenu;

class CategoryRepository extends CoreRepository
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getModelClass(){
        return Model::class;
    }

    public function buildMenu($arrMenu){
        $mBuilder = LavMenu::make('MyNav', function ($m) use ($arrMenu) {
            foreach($arrMenu as $item){
                if ($item->parent_id == 0){
                    $m->add($item->title, $item->id)
                        ->id($item->id);
                }else if ($find = $m->find($item->parent_id)){
                    $find->add($item->title, $item->id)
                        ->id($item->id);
                }
            }
        });

        return $mBuilder;
    }

    public function checkChildren($id){
        $children = $this->startConditions()
            ->where('parent_id', $id)
            ->count();
        return $children;
    }

    public function checkParentsProducts($id){
        $parents = \DB::table('products')
            ->where('category_id', $id)
            ->count();
        return $parents;
    }

    public function deleteCategory($id){
        $delete = $this->startConditions()
            ->find($id)
            ->forceDelete();
        return $delete;
    }

    public function getComboBoxCategories(){
        $columns = implode(',', [
           'id',
            'parent_id',
            'title',
            'CONCAT (ID, ". ", title) AS combo_title',
        ]);

        $result = $this->startConditions()
            ->selectRaw($columns)
            ->toBase()
            ->get();
        return $result;
    }

    public function checkUnigueName($name, $parent_id){
        //dump($name, $parent_id);
        $check = $this->startConditions()
            ->where('title', $name)
            ->where('parent_id', $parent_id)
            ->exists();
        return $check;
    }

}
