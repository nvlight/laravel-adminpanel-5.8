<?php

namespace App\Repositories\Admin;

use App\Repositories\CoreRepository;
use App\Models\Admin\AttributeGroup as Model;

class FilterGroupRepository extends CoreRepository
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
     * Get All records in table attribute_groups
     * @return array
     */
    public function getAllGroupsFilter(){
        $attr_group = \DB::table('attribute_groups')
            ->orderBy('id', 'DESC')
            ->get()->all();
        return $attr_group;
    }

    /**
     * Delete Group of Filter table->attribute_groups
     * @param $attributeGroup
     * @return mixed
     */
    public function deleteGroupFilter($attributeGroup){
        return $attributeGroup->delete();
    }

    /**
     * Count of all groups filter
     * @return void
     */
    public function getCountGroupFilter(){
        $count = \DB::table('attribute_values')->count();
        return $count;
    }
}
