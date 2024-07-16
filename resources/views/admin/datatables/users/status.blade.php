<div class="user-status">
    <i class="{{getClassIconUserStatus($user->status)}}"></i>
    <div class="text-status">
        <span class="{{getTextStatus($user->status)}}"><b>{{config('constant.user_status.' . $user->status)}}</b></span>
    </div>
</div>