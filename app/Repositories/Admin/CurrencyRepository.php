<?php

namespace App\Repositories\Admin;

use App\Models\Admin\Currency;
use App\Repositories\CoreRepository;
use App\Models\Admin\Currency as Model;
use App\SBlog\Core\MGDebug;

class CurrencyRepository extends CoreRepository
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
     * Get All Currency
     * @return void
     */
    public function getAllCurrency(){
        $currency = $this->startConditions()::all();
        return $currency;
    }

    public function swithBaseCure($currency){
        $id = Currency::where('base','1')
            ->pluck('id')
            ->toArray();
        //die(MGDebug::dump($id));

        //if (!count($id)){
        //    return ['success' => 0, 'msg' => 'Ошибка при изменении базовой валюты - не установлена базовая валюта по умолчанию!'];
        //}
        if (count($id)){
            $id = $id[0];
            $new = Currency::find($id);
            if (!$new){
                return ['success' => 0, 'msg' => 'Ошибка при изменении базовой валюты - не найдена запись с базовой валютой по умолчанию!'];
            }
            $new->base = '0';
            $new->save();
            if (!$new){
                return ['success' => 0, 'msg' => 'Ошибка при изменении базовой валюты - не сохранена запись с обнулением базовой валютой!'];
            }
        }

        $currency->base = '1';
        $save = $currency->save();
        if (!$save){
            return ['success' => 0, 'msg' => 'Ошибка при изменении базовой валюты - не сохранена запись с новой базовой валютой!'];
        }

        return ['success' => 1, 'msg' => 'Базовая валюта изменена!'];
    }

    /**
     * Delete currency
     * @param $currency
     * @return mixed
     */
    public function delete($currency){
        $delete = $currency->delete();
        return $delete;
    }
}
