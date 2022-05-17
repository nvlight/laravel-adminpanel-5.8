<?php

namespace App\Http\Controllers\Blog\Admin;

use App\Models\Admin\AttributeGroup;
use App\Models\Admin\AttributeValue;
use App\Repositories\Admin\FilterAttrRepository;
use App\Repositories\Admin\FilterGroupRepository;
use App\SBlog\Core\MGDebug;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use MetaTag;
use App\Http\Requests\BlogGroupFilterAddRequest;
use App\Http\Requests\BlogAttrsFilterAddRequest;

class FilterController extends AdminBaseController
{
    private $filterGroupRepository;
    private $filterAttrsRepository;

    public function __construct()
    {
        parent::__construct();
        $this->filterGroupRepository = app(FilterGroupRepository::class);
        $this->filterAttrsRepository = app(FilterAttrRepository::class);
    }

    /**
     * Show all groups of Filter table->attribute_group
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function attributeGroup(){
        $attrs_group = $this->filterGroupRepository->getAllGroupsFilter();

        MetaTag::setTags(['title' => 'Группы фильтров']);
        return view('blog.admin.filter.attribute-group',
            compact('attrs_group'));
    }

    /**
     * Add Group for Filter table->attribute_group
     * @return void
     */
    public function groupAdd(BlogGroupFilterAddRequest $request){
        if ($request->isMethod('post')){
            $data = $request->input();
            $group = (new AttributeGroup())->create($data);
            $group->save();

            if ($group) {
                return redirect()
                    ->route('blog.admin.filter-group')
                    ->with(['success' => 'Добавлена новая группа']);
            }else{
                return back()
                    ->withErrors(['msg' => 'Ошибка создания новой группы'])
                    ->withInput();
            }
        }else{
            MetaTag::setTags(['title' => 'Новая группа фильтров']);
            return view('blog.admin.filter.group-add-group');
        }
    }

    public function groupEdit(BlogGroupFilterAddRequest $request, AttributeGroup $attributeGroup){
        if ($request->isMethod('post')){
            $attributeGroup->title = $request->title;
            $save = $attributeGroup->save();
            if ($save){
                return redirect('/admin/filter/group-filter')
                    ->with(['success' => 'Успешно сохранено']);
            }else{
                return back()
                    ->withErrors(['msg' => 'Ошибка при сохранении группы'])
                    ->withInput();
            }
        }else{
            //dd($attributeGroup);
            MetaTag::setTags(['title' => 'Редактирование группы']);
            return view('blog.admin.filter.group-add-edit',
                ['group' => $attributeGroup]);
        }
    }

    public function groupDelete(AttributeGroup $attributeGroup){
        //return $attributeGroup;

        $count = $this->filterAttrsRepository->getCountFilterAttrsById($attributeGroup->id);

        if (!$count){
            $result = $this->filterGroupRepository->deleteGroupFilter($attributeGroup);

            if ($result){
                return redirect('/admin/filter/group-filter')
                    ->with(['success' => 'Успешно удалено']);
            }else{
                return back()
                    ->withErrors(['msg' => 'Ошибка при удалении группы'])
                    ->withInput();
            }
        }else{
            return back()
                ->withErrors(['msg' => 'Удаление не возможно, т.к. в данной группе есть атрибуты!'])
                ->withInput();
        }
    }

    /**
     * Show all attributes for Filters table->attribute_values
     * @return void
     */
    public function attributeFilter(){

        $attrs = $this->filterAttrsRepository->getAllAttrsFilter();
        $count = $this->filterGroupRepository->getCountGroupFilter();

        MetaTag::setTags(['title' => 'Фильтры']);
        return view('blog.admin.filter.attribute',
            compact('attrs','count'));
    }

    /**
     * Add attribute Value
     * @param AttributeValue $attributeValue
     * @return void
     */
    public function attributeAdd(BlogAttrsFilterAddRequest $request, AttributeValue $attributeValue){
        if ($request->isMethod('post')){
            //die(MGDebug::dump($request->all()));
            $uniqueName = $this->filterAttrsRepository->checkUnigue($request->value);
            if ($uniqueName){
                return back()
                    ->withErrors(['msg' => 'Такой фильтр уже есть.'])
                    ->withInput();
            }
            $newAttributeValue = (new AttributeValue())
                ->make(($request->all()))->save();
            if ($newAttributeValue){
                return redirect()
                    ->route('blog.admin.filter.attribute')
                    ->with(['success' => 'Атрибут добавлен']);
            }else{
                return back()
                    ->withErrors(['msg' => 'Ошибка при создании фильтра'])
                    ->withInput();
            }
        }else{
            $group = $this->filterGroupRepository->getAllGroupsFilter();
            MetaTag::setTags(['title' => 'Новый атрибут для фильтра']);
            return view('blog.admin.filter.attrs-add', compact('group'));
        }
    }

    /**
     * Edit attribute Value
     * @param AttributeValue $attributeValue
     * @return void
     */
    public function attributeEdit(BlogAttrsFilterAddRequest $request, AttributeValue $attributeValue){
        //return $attributeValue;
        if ($request->isMethod('post')){
            //echo MGDebug::dump($attributeValue->toArray());
            //echo MGDebug::dump($request->all());
            //die;

            $attributeValue->attr_group_id = $request->attr_group_id;
            $attributeValue->value = $request->value;
            $save = $attributeValue->save();
            if ($save){
                return redirect()
                    ->route('blog.admin.filter.attribute-edit', $attributeValue->id)
                    ->with(['success' => 'Атрибут обновлен!']);
            }else{
                return back()
                    ->withErrors(['msg' => 'Ошибка при обновлении фильтра'])
                    ->withInput();
            }
        }else{
            $groups = $this->filterGroupRepository->getAllGroupsFilter();
            $attr = $attributeValue;
            MetaTag::setTags(['title' => 'Редактирование фильтра']);
            return view('blog.admin.filter.attrs-edit', compact('groups', 'attr'));
        }
    }

    /**
     * Delete attribute Value
     * @param AttributeValue $attributeValue
     * @return void
     */
    public function attributeDelete(AttributeValue $attributeValue){
        //return $attributeValue;
        $delete = $attributeValue->delete();
        if ($delete){
            return redirect()
                ->route('blog.admin.filter.attribute', $attributeValue->id)
                ->with(['success' => 'Атрибут удален!']);
        }else{
            return back()
                ->withErrors(['msg' => 'Ошибка при удалении фильтра'])
                ->withInput();
        }
    }
}
