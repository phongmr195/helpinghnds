<div class="btn-group btn-action">
    <a data-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-ellipsis-h"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-right" style="">
        @if(isset($user))
            <a class=" dropdown-item btn btn-info btn-sm" href="{{route(getRouteNameUserDetail($user->user_type), ['user' => $user->id])}}">
                <i class="fas fa-edit"></i> View
            </a>
            <span class="dropdown-item">
                <i class="fas fa-user"></i>
                    <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-update-status-{{$user->id}}">
                        Active /  Inactive
                    </a>
            </span>
            @if($user->user_type != 'admin')
                <a href="javascript:void(0)" class="dropdown-item btn btn-danger btn-sm js_remove_user" data-id="{{$user->id}}" data-name="{{$user->user_type}}" data-url="{{route('admin.users.delete', ['user' => $user->id])}}">
                    <i class="fas fa-trash"></i> Delete
                </a>
            @endif
        @endif
    </div>
</div>

<!-- Popup Update Profile-->
<div class="modal fade modal-update-status wrap-content-detail" id="modal-update-status-{{$user->id}}" style="display: none;" aria-hidden="true">
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
                                    <select id="inputStatus" class="form-control custom-select js_select_status_{{$user->id}}" name="status">
                                        <option value="1" {{$user->status == 1 ? 'selected' : ''}}>Active</option>
                                        <option value="0" {{$user->status == 0 ? 'selected' : ''}}>Inactive</option>
                                        <option value="2" {{$user->status == 2 ? 'selected' : ''}}>Pending</option>
                                        <option value="3" {{$user->status == 3 ? 'selected' : ''}}>Rejected by admin</option>
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <button type="button" class="btn btn-sm btn-success form-control js_update_status" data-id="{{$user->id}}" data-url="{{route('admin.users.update-status', ['user' => $user->id])}}">
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
<!-- END Popup Update Profile-->
