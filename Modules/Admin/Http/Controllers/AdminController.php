<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Admin\Http\Requests\AdminRequest;
use Modules\Admin\Services\admin\AdminService;
use Modules\Common\Exceptions\ApiException;

class AdminController extends Controller
{

    function index(Request $request)
    {
        return (new AdminService())->index($request->only(['page','limit','status','name']));
    }
    function edit(Request $request)
    {
        return (new AdminService())->edit($request->only(['avatarUrl','status','id','account','password','name','role_id','privilege_cate']));
    }

    public function del(Request $request){
        return (new AdminService())->del($request->input('id'));
    }


    public function create(AdminRequest $request)
    {

        throw new ApiException('11',500);

        return $request->input('name');

    }

    public function create1(AdminRequest $request){
        echo 1;
    }

//    public function index()
//    {
////        echo json_encode(['code'=>200]);die;
//        return (['code'=>200]);
//        return view('admin::index');
//    }

    public function login(Request $request){
        if($request->input('data')!='{"username":"admin","password":"111111"}'){
            $info_token = [
                'code'=> 40000,
                'data'=> [],
                'message'=>'账号或密码错误'
            ];

            return json_encode($info_token);
        }
        $info_token = [
            'code'=> 20000,
            'data'=> ['token'=> csrf_token()]
        ];

        return json_encode($info_token);
    }

    public function logout(){
        $info_token = [
            'code'=> 20000,
            'data'=> []
        ];

        return json_encode($info_token);
    }

    public function info()
    {
        $adminData = (new AdminService())->getAdminInfo();
        $info_token = [
            'code'=> 20000,
            'data'=> [
                    'roles'=> ["admin"],
                    'introduction'=> "I am a super administrator",
                    'avatar'=> $adminData['avatarUrl'],
                    'name'=> $adminData['name']]
        ];

        return json_encode($info_token);

        $info_t = [
            'cookie_needed'=> false,
'entropy'=> 43395758,
'origins'=> ["*:*"],
'websocket'=> true
        ];

    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('admin::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
//    public function edit($id)
//    {
//        return view('admin::edit');
//    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
