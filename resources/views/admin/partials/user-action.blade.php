
<div class="btn-group btn-action">
    <a data-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-ellipsis-h"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-right" style="">
        @if(isset($item))
            <a class=" dropdown-item btn btn-info btn-sm" href="{{route(getRouteNameUserDetail($item->user_type), ['user' => $item->id])}}">
                <i class="fas fa-edit"></i> Edit
            </a>
            <span class="dropdown-item">
                <i class="fas fa-user"></i>
                    <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-update-status-{{$item->id}}">
                        Active /  Inactive
                    </a>
            </span>
            @if($item->user_type != 'admin')
                <a href="javascript:void(0)" class="dropdown-item btn btn-danger btn-sm js_remove_user" data-id="{{$item->id}}" data-name="{{$item->user_type}}" data-url="{{route('admin.users.delete', ['user' => $item->id])}}">
                    <i class="fas fa-trash"></i> Delete
                </a>
            @endif
        @endif
    </div>
</div>

<!-- Popup Update Profile-->
@include('admin.modals.update-status', ['item' => $item])
<!-- END Popup Update Profile-->
