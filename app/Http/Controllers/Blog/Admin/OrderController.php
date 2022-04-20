<?php

namespace App\Http\Controllers\Blog\Admin;

use App\Http\Requests\AdminOrderSaveRequest;
use App\Models\Admin\Order;
use App\Repositories\Admin\MainRepository;
use App\Repositories\Admin\OrderRepository;
use Illuminate\Http\Request;

class OrderController extends AdminBaseController
{
    private $orderRepository;

    public function __construct()
    {
        parent::__construct();
        $this->orderRepository = app(OrderRepository::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perPage = 10;
        \MetaTag::setTags(['title' => 'Список заказов']);
        $paginator = $this->orderRepository->getAllOrders($perPage);
        $countOrders = MainRepository::getCountOrders();
        //dump($paginator);

        return view('blog.admin.order.index', compact('paginator','countOrders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = $this->orderRepository->getId($id);
        if (!($item)){
            abort(404);
        }

        $order = $this->orderRepository->getOneOrder($item->id);
        if (!($order)){
            abort(404);
        }

        $order_products = $this->orderRepository->getAllOrderProductsId($item->id);
        //dump($order);
        \MetaTag::setTags(['title' => "Заказ №{$order->id}"]);

        return view('blog.admin.order.edit',
            compact('order_products', 'order'));
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = $this->orderRepository->changeStatusOnDelete($id);

        if ($order){
            $result = Order::destroy($id);
            if ($result){
                return redirect()
                    ->route('blog.admin.orders.index')
                    ->with(['success' => "Record #{$id} deleted!"]);
            }else{
                return back()
                    ->withErrors(['msg' => 'Error with delete!']);
            }
        }else{
            return back()
                ->withErrors(['msg' => 'Status change error!']);
        }
    }

    public function change($id){
        $result = $this->orderRepository->changeStatusOrder($id);

        //dump($result);
        //dd('where am i*');

        if ($result){
            //dd('why!');
            return redirect()
                ->route('blog.admin.orders.edit', $id)
                ->with(['success' => 'Edit saved!']);
        }else{
            return back()
                ->withErrors(['msg' => 'Save error!']);
        }
    }
    public function save(AdminOrderSaveRequest $request, $id){
        $result = $this->orderRepository->saveOrderComment($id);

        if ($result){
            return redirect()
                ->route('blog.admin.orders.edit', $id)
                ->with(['success' => 'Edit saved!']);
        }else{
            return back()
                ->withErrors(['msg' => 'Save error!']);
        }
    }

    public function forcedestroy($id){

//        dump($id);
//        $order = $this->orderRepository->getId($id);
//        dump($order);
//        if (!($order)){
//            abort(404);
//        }

        $result = \DB::table('orders')
            ->delete($id);

        if ($result){
            return redirect()
                ->route('blog.admin.orders.index', $id)
                ->with(['success' => 'Delete success!']);
        }else{
            return back()
                ->withErrors(['msg' => 'Delete error!']);
        }
    }
}
