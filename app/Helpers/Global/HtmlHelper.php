<?php

use App\Models\CashOutLog;

if(!function_exists('getClassIconUserStatus')){
    function getClassIconUserStatus(int $status)
    {
        switch (true) {
            case $status == 0:
                $class = 'far fa-times-circle fa-in-active';
                break;
            case $status == 2:
                $class = 'far fa-clock fa-warning';
                break;
            case $status == 3:
                $class = 'fas fa-minus-circle fa-reject';
                break;
            default:
                $class = 'far fa-check-circle fa-active';
        }
        return $class;  
    }
}

if(!function_exists('getClassIconOrderStatus')){
    function getClassIconOrderStatus(int $status)
    {
        switch (true) {
            case $status == 0:
                $class = 'fas fa-clock fa-warning';
                break;
            case in_array($status, [1, 2, 3, 4, 5, 8]):
                $class = 'far fa-clock fa-pending';
                break;
            case in_array($status, [7, 12]):
                $class = 'fas fa-minus-circle fa-reject';
                break;
            case $status == 6: // Order status done
                $class = 'far fa-check-circle fa-active ';
                break;
            default:
                $class = 'far fa fa-question-circle';
        }
        return $class;  
    }
}

if(!function_exists('getClassUserStatus')){
    function getClassUserStatus($status)
    {
        switch (true) {
            case $status == 0:
                $class = 'badge-danger';
                break;
            case $status == 2:
                $class = 'badge-info';
                break;
            case $status == 3:
                $class = 'badge-danger';
                break;
            default:
                $class = 'badge-success';
        }
        return $class;  
    }
}

if(!function_exists('getClassPaymentStatus')){
    function getClassPaymentStatus($status)
    {
        switch (true) {
            case $status == 2:
                $class = 'badge-danger';
                break;
            case $status == 0:
                $class = 'badge-info';
                break;
            case $status == 3:
                $class = 'badge-success';
                break;
            default:
                $class = 'badge-success';
        }
        return $class;  
    }
}

if(!function_exists('getClassCashoutStatus')){
    function getClassCashoutStatus($status)
    {
        switch (true) {
            case in_array($status, [2, 3]):
                $class = 'badge-danger';
                break;
            case $status == 0:
                $class = 'badge-info';
                break;
            default:
                $class = 'badge-success';
        }
        return $class;  
    }
}

if(!function_exists('getClassOrderStatus')){
    function getClassOrderStatus($status)
    {
        switch (true) {
            case $status == 0:
                $class = 'badge-warning';
                break;
            case in_array($status, [1, 2, 3, 4, 5, 8]):
                $class = 'badge-info';
                break;
            case in_array($status, [7, 12]):
                $class = 'badge-danger';
                break;
            default:
                $class = 'badge-success';
        }
        return $class;  
    }
}

if(!function_exists('getTextStatus')){
    function getTextStatus($status)
    {
        switch (true) {
            case in_array($status, [0, 3]):
                $class = 'text-danger';
                break;
            case $status == 2:
                $class = 'text-warning';
                break;
            default:
                $class = 'text-success';
        }
        return $class;  
    }
}

if(!function_exists('createJsCustomLink')){
    function createJsClassForMenuItem(string $slug)
    {
        return 'js_' .$slug. '_link';
    }
}

if(!function_exists('showRatingStar')){
    function showRatingStar($rating)
    {
        $stars = "";
        $numMax = 5;
        if(!is_null($rating) || !empty($rating)){
            $whole = floor($rating);
            $fraction = $rating - $whole;
            
            switch (true) {
                case $fraction < .25:
                    $dec = 0;
                    break;
                case $fraction >= .25 && $fraction < .75:
                    $dec = .50;
                    break;
                case $fraction >= .75:
                    $dec = 1;
                    break;
            }
    
            $r = $whole + $dec;
            $numBlankStar = (int)ceil($numMax - $r);
            $numBlankStar = ($numBlankStar + $r) > $numMax ? $numBlankStar - 1 : $numBlankStar;
            $newwhole = floor($r);
            $fraction = $r - $newwhole;
    
            for($s = 1; $s <= $newwhole; $s++){
                $stars .= '<i class="fas fa-star"></i>';	
            }
    
            if($fraction == .5){
                $stars .= '<i class="fas fa-star-half-alt"></i>';	
            }
    
            for($i = 1; $i <= $numBlankStar; $i ++){
                $stars.= '<i class="far fa-star"></i>';
            }

            return $stars;
        }

        for($i = 1; $i <= $numMax; $i ++){
            $stars.= '<i class="far fa-star"></i>';
        }
        
        return $stars;
    }
}


if(!function_exists('getAvatarHtml')){
    function getAvatarHtml($user)
    {
        $html = '<i class="far fa-3x fa-user-circle"></i>';
        if(!is_null($user->profile) && isImage($user->profile->avatar)){
            $html = '<img class="img-avatar lozad" data-src="'. getPathImageUpload($user->profile->avatar) . '" width=48 height=48 alt="avatar">';
        }
        return $html;
    }
}

if(!function_exists('getActionHtml')){
    function getActionHtml($user)
    {
        $actionDelete = '';
        if($user->user_type != 'admin'){
            $name = $user->user_type == 'worker' ? "worker $user->name" : "customer $user->name";
            $actionDelete = 
                '<a href="javascript:void(0)" class="dropdown-item btn btn-danger btn-sm js_remove_user" data-id="'.$user->id.'" data-name="'.$name.'" data-url="'.route('admin.users.delete', ['user' => $user->id]).'">
                    <i class="fas fa-trash"></i> Delete
                </a>';
        }

        $options = '';
        foreach (config('constant.user_status_select') as $key => $value){
            $selected = $user->status == $key ? 'selected' : '';
            $options.= '<option value="'.$key.'" ' .$selected. '>'.$value.'</option>';
        }

        return '
            <div class="btn-group btn-action">
                <a data-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-h"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right" style="">
                    <a class=" dropdown-item btn btn-info btn-sm" href="'.route(getRouteNameUserDetail($user->user_type), ['user' => $user->id]).'">
                        <i class="fas fa-edit"></i> View
                    </a>
                    <span class="dropdown-item">
                        <i class="fas fa-user"></i>
                            <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-update-status-'.$user->id.'">
                                Active /  Inactive
                            </a>
                    </span>
                    '.$actionDelete.'
                </div>
            </div>
            <!-- Popup Update Profile-->
            <div class="modal fade modal-update-status wrap-content-detail" id="modal-update-status-'.$user->id.'" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Update profile</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- Status -->
                            <div class="card">
                                <div class="card-header bg-info">
                                    <div class="head-title">
                                        <h4>
                                            Status
                                        </h4>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="tab-content">
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <select id="inputStatus" class="form-control custom-select js_select_status_'.$user->id.'" name="status">
                                                    '.$options.'
                                                </select>
                                            </div>
                                            <div class="col-sm-4">
                                                <button type="button" class="btn btn-sm btn-success form-control js_update_status" data-id="'.$user->id.'" data-url="'.route('admin.users.update-status', ['user' => $user->id]).'">
                                                    Update
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.tab-content -->
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- END Status -->
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- END Popup Update Profile-->';
    }
}


if(!function_exists('getActionUserAccountHtml')){
    function getActionUserAccountHtml($user)
    {
        return '
            <div class="btn-group btn-action">
                <a data-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-h"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right" style="">
                    <a class=" dropdown-item btn btn-info btn-sm js_show_modal_update_account" data-id="'.$user->id.'">
                        <i class="far fa-edit"></i> Update
                    </a>
                    <span class="dropdown-item">
                        <i class="fas fa-unlock"></i>
                        <a href="javascript:void(0)" style="font-size:.875rem" class="js_show_modal_reset_pass" data-url="'.route('admin.users.reset-password', ['user' => $user->id]).'">
                            Change password
                        </a>
                    </span>
                    <a href="javascript:void(0)" class="dropdown-item btn btn-danger btn-sm js_remove_item" data-id="'.$user->id.'" data-name="'.$user->user_type.'" data-url="'.route('admin.users.delete', ['user' => $user->id]).'">
                        <i class="fas fa-trash"></i> Delete
                    </a>
                </div>
            </div>';
    }
}

if(!function_exists('getActionCashoutHtml')){
    function getActionCashoutHtml($cashout)
    {
        $disabled = ($cashout->status == 1 || $cashout->status == 2) ? 'disable-links' : '';
        return '
            <div class="btn-group btn-action">
                <a data-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-h"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right" style="">
                    <a class="dropdown-item btn btn-info btn-sm js_approve_cashout '.$disabled.'" data-id="'.$cashout->id.'" data-url="'.route('admin.ajax.approve_cashout').'">
                        <i class="fas fa-check icon-success"></i> Approved
                    </a>
                    <a href="javascript:void(0)" class="dropdown-item btn btn-info btn-sm js_show_modal_cancel_cashout '.$disabled.'" data-id="'.$cashout->id.'">
                        <i class="fas fa-times icon-danger"></i> Canceled
                    </a>
                </div>
            </div>';
    }
}

if(!function_exists('createBadgeWaitingForCashout')){
    function createBadgeWaitingForCashout($menuSlug)
    {
        $cashoutWaitingCount = CashOutLog::where('status', 0)->count();
        if($menuSlug == 'cashout' && isset($cashoutWaitingCount) && $cashoutWaitingCount != 0){
            return '<span class="badge badge-danger right">' .$cashoutWaitingCount. '</span>';
        }

        return '';
    }
}