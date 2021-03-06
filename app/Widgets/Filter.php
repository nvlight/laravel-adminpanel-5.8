<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

class Filter extends AbstractWidget
{
    public $groups;
    public $attrs;
    public $tpl;
    public $filter;

    protected $config = [

    ];

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->filter = $this->config['filter'];
        $this->tpl    = $this->config['tpl'];
    }

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $this->groups = $this->getGroups();
        $this->attrs  = $this->getAttrs();

        return view($this->tpl, [
            'config' => $this->config,
            'groups' => $this->groups,
            'attrs'  => $this->attrs,
            'filter' => $this->filter,
        ]);
    }

    protected function getAttrs(){
        $data = \DB::table('attribute_values')->get();
        $attrs = [];
        foreach($data as $k => $v){
            $attrs[$v->attr_group_id][$v->id] = $v->value;
        }
        return $attrs;
    }

    protected function getGroups(){
        $data = \DB::table('attribute_groups')->get();
        $groups = [];
        foreach($data as $v){
            $groups[$v->id] = $v->title;
        }
        return $groups;
    }

    public function getFilter(){
        $filter = null;
    }
}
