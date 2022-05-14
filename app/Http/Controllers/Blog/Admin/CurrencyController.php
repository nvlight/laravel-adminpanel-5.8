<?php

namespace App\Http\Controllers\Blog\Admin;

use App\Http\Controllers\Blog\BaseController;
use App\Http\Requests\AdminCurrencyAddRequest;
use App\Models\Admin\Currency;
use App\SBlog\Core\MGDebug;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Admin\CurrencyRepository;
use MetaTag;

class CurrencyController extends AdminBaseController
{
    private $currencyRepository;

    public function __construct()
    {
        parent::__construct();
        $this->currencyRepository = app(CurrencyRepository::class);
    }

    public function index(){
        $currency = $this->currencyRepository->getAllCurrency();

        MetaTag::setTags(['title' => 'Валюта магазина']);
        return view('blog.admin.currency.index', compact('currency'));
    }

    /**
     * Add new Currency
     * @param AdminCurrencyAddRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function add(AdminCurrencyAddRequest $request){
        if ($request->isMethod('post')) {
            $currency = new Currency();
            //die(MGDebug::dump($request->all()));
            $currency->fill($request->all());

            if ($currency->base) {
                $result = $this->currencyRepository->swithBaseCure($currency);
                if (!$result['success']){
                    return back()
                        ->withErrors(['msg' => $result['msg']])
                        ->withInput();
                }
            }
            $save = $currency->save();
            if ($save){
                return redirect()
                    ->route('blog.admin.currency')
                    ->with(['success' => 'Валюта добавлена!']);
            }else{
                return back()
                    ->withErrors(['msg' => 'Ошибка при добавлении валюты!'])
                    ->withInput();
            }
        }else{
            $currency = new Currency();
            MetaTag::setTags(['title' => 'Добавление валюты']);

            return view('blog.admin.currency.add',
                compact('currency'));
        }

    }

    /**
     * Edit currency
     * @param AdminCurrencyAddRequest $request
     * @param Currency $currency
     * @return Currency
     */
    public function edit(AdminCurrencyAddRequest $request, Currency $currency){
        //return $currency;
        if ($request->isMethod('post')){
            //dump($request->all()); die;
            //dump($currency); die;
            $currency->fill($request->all());
            if ($currency->base) {
                $result = $this->currencyRepository->swithBaseCure($currency);
                if (!$result['success']){
                    return back()
                        ->withErrors(['msg' => $result['msg']])
                        ->withInput();
                }
            }
            $save = $currency->save();
            if ($save){
                return redirect()
                    ->route('blog.admin.currency')
                    ->with(['success' => 'Валюта обновлена!']);
            }else{
                return back()
                    ->withErrors(['msg' => 'Ошибка при обновлении валюты!'])
                    ->withInput();
            }
        }else{
            MetaTag::setTags(['title' => 'Редактирование валюты']);

            return view('blog.admin.currency.edit',
                compact('currency'));
        }
    }

    public function delete(Currency $currency){
        $result = $this->currencyRepository->delete($currency);
        if ($result){
            return redirect()
                ->route('blog.admin.currency')
                ->with(['success' => 'Валюта удалена!']);
        }
        return back()
            ->withErrors(['msg' => 'Ошибка при удалении валюты!'])
            ->withInput();
    }
}
