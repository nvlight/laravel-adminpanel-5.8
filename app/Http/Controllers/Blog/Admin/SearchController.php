<?php

namespace App\Http\Controllers\Blog\Admin;

use App\Http\Controllers\Blog\BaseController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use MetaTag;

class SearchController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * For Show search results
     * @return void
     */
    public function index(Request $request){
        $query = !empty(trim($request->search)) ? trim($request->search) : null;
        $products = \DB::table('products')
            ->where('title','LIKE', '%'.$query.'%')
            ->get()
            ->all();
        $currency = \DB::table('currencies')
            ->where('base', '=', '1')
            ->first();

        MetaTag::setTags(['title' => 'Результаты поиска']);
        return view('blog.admin.search.result',
            compact('query','products', 'currency'));
    }

    /**
     * For ajax Search
     * @return void
     */
    public function search(Request $request){
        $search = $request->get('term');
        $result = \DB::table('products')
            ->where('title', 'LIKE', '%'.$search.'%')
            ->pluck('title');
        return response()->json($result);
    }
}
