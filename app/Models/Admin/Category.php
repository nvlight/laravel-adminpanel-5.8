<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'title',
        'alias',
        'parent_id',
        'keywords',
        'description',
        'created_at',
        'updated_at',
        'deletad_at',
    ];

    /** for search category children in edit category */
    public function children(){
        return $this
            ->hasMany('App\Models\Admin\Category','parent_id');
    }
}
