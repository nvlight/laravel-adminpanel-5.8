<?php

namespace App\Repositories\Admin;

use App\Models\Admin\AttributeValue as Model;
use App\Repositories\CoreRepository;

class FilterAttrRepository extends CoreRepository
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getModelClass()
    {
        return Model::class;
    }

    /**
     * Get Count Attributes Filter by id
     * @param $id
     * @return int
     */
    public function getCountFilterAttrsById($id){
        $count = \DB::table('attribute_values')
            ->where('attr_group_id', $id)
            ->count();
        return $count;
    }

    /**
     * Get All Attribute filter with name Group
     * @return void
     */
    public function getAllAttrsFilter(){
        $attrs = \DB::table('attribute_values')
            ->join('attribute_groups','attribute_groups.id','=','attribute_values.attr_group_id')
            ->select('attribute_values.*','attribute_groups.title')
            ->orderBy('attribute_values.id', 'DESC')
            ->paginate(10);
        return $attrs;
    }

    /**
     * Check is attribute value is unique
     * @param $name
     * @return mixed
     */
    public function checkUnigue($name){
        $unigue = $this->startConditions()
            ->where('value', $name)
            ->count()
        ;

        return $unigue;
    }
}
