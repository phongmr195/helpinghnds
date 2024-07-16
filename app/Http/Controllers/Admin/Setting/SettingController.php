<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Services\Admin\SettingService;
use App\Http\Requests\Admin\AddSettingRoleRequest;
use App\Http\Requests\Admin\EditSettingRoleRequest;
use App\Models\Page;
use App\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;

use function GuzzleHttp\Promise\all;

class SettingController extends Controller
{
    use ApiResponser;
    protected $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    public function index()
    {
        $roles = Role::select('id', 'name')->orderBy('id','desc')->get();
        $pages = Page::select('id', 'name', 'slug', 'order', 'route_name')->get();
        return view('admin.settings.index', compact('roles', 'pages'));
    }

    public function viewAdd()
    {
        $permissions = Permission::get();
        return view('admin.settings.add', compact('permissions'));
    }

    public function addRole(AddSettingRoleRequest $request)
    {
        $role = Role::create(['name' => $request->name]);
        // $arrRoleNames = ['root', 'admin'];
        // $roles = Role::select('id', 'name')->with('page')->whereNotIn('name', $arrRoleNames)->get();
        // $htmlOptionRole = view('admin.partials.list-option-role', ['roles' => $roles])->render();
        if(!empty($request->permission)){
            $role->syncPermissions($request->permission);
        }

        $roles = Role::select('id', 'name')->orderBy('id','desc')->get();
        $pages = Page::select('id', 'name', 'slug', 'order', 'route_name')->get();
        return view('admin.settings.index', compact('roles', 'pages'));
    }

    public function viewEditRole($id)
    {
        $role = Role::find($id);
        $rolePages = [];
        if($role->page){
            $rolePages = json_decode($role->page->page_ids) ?? [];
        }
        $pages = Page::select('id', 'name')->orderBy('order', 'asc')->get();
        $permissions = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();
    
        return view('admin.settings.edit',compact('role','permissions','rolePermissions', 'pages', 'rolePages'));
    }

    public function detail($id)
    {
        $role = Role::find($id);
        $permissions = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();
    
        return view('admin.settings.detail',compact('role','permissions','rolePermissions'));
    }

    public function editRole(Request $request)
    {
        $roleID = $request->role_id;
        $role = Role::with('page')->findOrFail($roleID);
        $role->update(['name' => $request->name]);
        if(is_null($role->page)){
            $role->page()->create([
                'role_id' => $roleID,
                'page_ids' => json_encode($request->page_ids)
            ]);
        } else {
            $role->page()->update([
                'role_id' => $roleID,
                'page_ids' => json_encode($request->page_ids)
            ]);    
        }

        if(!empty($request->permission)){
            $role->syncPermissions($request->permission);
        }

        $rolePages = $request->page_ids ?? [];
        $pages = Page::select('id', 'name')->orderBy('order', 'asc')->get();
        $permissions = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $roleID)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();
        $alert_message = 'Update role success!';
        return view('admin.settings.edit',compact('role','permissions','rolePermissions', 'pages', 'rolePages', 'alert_message'));
    }

    public function getDataEditRole(Request $request)
    {
        $roleData = explode('_', $request->role_data);
        $roleID = $roleData[0];
        $role = Role::with('page')->findOrFail($roleID);
        $rolePages = [];
        if($role && $role->page){
            $rolePages = json_decode($role->page->page_ids) ?? [];
        }
        $pages = Page::select('id', 'name')->orderBy('order', 'asc')->get();

        $htmlPage = view('admin.partials.table-page', ['pages' => $pages, 'rolePages' => $rolePages])->render();

        if($role){
            return $this->success(['html_page' => $htmlPage]);
        }

        return $this->error();
    }
}
