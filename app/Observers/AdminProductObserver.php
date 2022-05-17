<?php

namespace App\Observers;

use App\Models\Admin\Product;
use Illuminate\Support\Carbon;

class AdminProductObserver
{
    public function creating(Product $product){
        $this->setAlias($product);
    }

    /**
     * Handle the product "created" event.
     *
     * @param  Product  $product
     * @return void
     */
    public function created(Product $product)
    {
        //
    }

    /**
     * Handle the product "updated" event.
     *
     * @param  Product  $product
     * @return void
     */
    public function updated(Product $product)
    {
        //
    }

    /**
     * Handle the product "deleted" event.
     *
     * @param  Product  $product
     * @return void
     */
    public function deleted(Product $product)
    {
        //
    }

    /**
     * Handle the product "restored" event.
     *
     * @param  Product  $product
     * @return void
     */
    public function restored(Product $product)
    {
        //
    }

    /**
     * Handle the product "force deleted" event.
     *
     * @param  Product  $product
     * @return void
     */
    public function forceDeleted(Product $product)
    {
        //
    }

    public function setAlias(Product $product){
        if (!$product->alias){
            $product->alias = \Str::slug($product->title);
            $check = Product::where('alias', $product->alias)->exists();

            if ($check){
                $product->alias = \Str::slug($product->title . '_' . \Str::random(111) . '_' . time());
            }
        }
    }

    public function saving(Product $product){
        $this->setPublishAt($product);
    }

    public function setPublishAt(Product $product){
        $needSetPublished = !$product->updated_at || $product->created_at;

        if ($needSetPublished){
            $product->updated_at = Carbon::now();
        }
    }
}
