<?php

namespace App\Http\Controllers\Blog\Admin;

use App\Http\Requests\AdminUserEditRequest;
use App\Models\Admin\User;
use App\Models\UserRole;
use App\Repositories\Admin\MainRepository;
use App\Repositories\Admin\UserRepository;
use Illuminate\Http\Request;
use MetaTag;

class UserController extends AdminBaseController
{
    private $userRepository;

    public function __construct(){
        parent::__construct();
        $this->userRepository = app(UserRepository::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perpage = 8;
        $countUsers = MainRepository::getCountUsers();
        $paginator = $this->userRepository->getAllUsers($perpage);

        MetaTag::setTags(['title' => 'Список пользователей']);
        return view('blog.admin.user.index', compact('countUsers', 'paginator'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        MetaTag::setTags(['title' => 'Добавление пользователя']);
        return view('blog.admin.user.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminUserEditRequest $request)
    {
        //dump($request->all());
        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
        ]);

        if (!$user){
            return back()
                ->withErrors(['msg' => 'Ошибка при добавлении пользователя'])
                ->withInput();
        }

        $role = UserRole::create([
            'user_id' => $user->id,
            'role_id' => (int)$request['role'],
        ]);
        if (!$role){
            return back()
                ->withErrors(['msg' => 'Ошибка при добавлении пользователя'])
                ->withInput();
        }
        return redirect()
            ->route('blog.admin.users.index', $user->id)
            ->with(['msg' => 'Успешно добавлен новый пользователь']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        dump('show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $perpage = 10;
        $item = $this->userRepository->getId($id);
        if (!$item){
            abort(404);
        }

        $orders = $this->userRepository->getUserOrders($id, $perpage);
        //dump($orders);
        $role = $this->userRepository->getUserRole($id);
        //dump($role->id);
        $count = $this->userRepository->getCountOrderPag($id);
        //dump($count);
        $count_orders = $this->userRepository->getCountOrders($id, $perpage);
        //dump($count_orders);
        //die;

        MetaTag::setTags(['title' => "Редактирование пользователя №{$item->id}"]);
        return view('blog.admin.user.edit', compact('orders','role','count','count_orders', 'item'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdminUserEditRequest $request, User $user,  UserRole $role)
    {
        $user->name = $request['name'];
        $user->email = $request['email'];
        $request['password'] == null ?:$user->password = bcrypt($request['password']);
        $save = $user->save();

        if (!$user){
            return back()
                ->withErrors(['msg' => 'Ошибка при сохранении пользователя'])
                ->withInput();
        }

        $role->where('user_id', $user->id)
            ->update(['role_id' => (int)$request['role']]);
        return redirect()
            ->route('blog.admin.users.edit', $user->id)
            ->with(['success' => 'Успешно обновлено']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $userName = $user->name;
        $result = $user->forceDelete();
        //$result = 1;

        if (!$result){
            return back()->withErrors(['msg' => 'Ошибка удаления']);
        }

        return redirect()->route('blog.admin.users.index')
            ->with(['success' => "Пользователь " . ucfirst($userName) . " удален"]);
    }
}
