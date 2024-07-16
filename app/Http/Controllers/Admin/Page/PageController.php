<?php

namespace App\Http\Controllers\Admin\Page;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Component;
use App\Models\Page;
use Illuminate\Http\Request;
use App\Services\Admin\UserService;

class PageController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    /**
     * Page Not Found 404
     */
    public function page404()
    {
        return view('admin.pages.page-404');
    }

    /**
     * Page Error 500
     */
    public function page500()
    {
        return view('admin.pages.page-500');
    }

    public function listCustomer(Request $request)
    {
        // $pageId = 1;
        // $page = Page::findOrFail($pageId);
        // $pageDetail = $page->pageDetail;
        // $block = Block::findOrFail($pageDetail->block_id);
        // $component_names = Component::whereIn('id', json_decode($block->component_ids))
        //     ->select('id', 'name', 'value')
        //     ->get();

        // return view('admin.pages.base', compact('page', 'pageDetail', 'block', 'component_names', 'datatable'));
    }

    /**
     * Report page
     */
    public function getReport()
    {
        return view('admin.pages.report');
    }
}
