<div class="modal fade" id="modal-update-profile" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
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
                    <div class="card-header bg-info p-custom">
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
                                <div class="col-sm-8 form-group">
                                    <select id="inputStatus" class="form-control custom-select js_select_status_{{$userDetail->id}}" name="status">
                                        <option value="1" {{$userDetail->status == 1 ? 'selected' : ''}}>Active</option>
                                        <option value="0" {{$userDetail->status == 0 ? 'selected' : ''}}>Inactive</option>
                                        <option value="2" {{$userDetail->status == 2 ? 'selected' : ''}}>Pending</option>
                                        <option value="3" {{$userDetail->status == 3 ? 'selected' : ''}}>Rejected by admin</option>
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <button type="button" class="btn btn-sm btn-success form-control js_update_status" data-id="{{$userDetail->id}}" data-url="{{route('admin.users.update-status', ['user' => $userDetail->id])}}">
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

                <!-- Reset password -->
                <div class="card">
                    <div class="card-header bg-info p-custom">
                        <div class="head-title">
                            <h4>
                                Reset password for worker
                            </h4>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content text-center">
                            <a href="" class="bt-reset-password" data-toggle="modal" data-target="#modal-reset-password"><i class="fas fa-sync-alt"></i> Click here to reset password</a>
                        </div>
                        <!-- /.tab-content -->
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- END Reset password -->
                <!-- Profile -->
                <div class="card">
                    <div class="card-header bg-info p-custom">
                        <div class="head-title">
                            <h4>
                                Profile
                            </h4>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <form class="form-horizontal form_profile_information js_form_profile_inforamtion" action="" method="POST">
                                @csrf
                                <div class="form-group row">
                                    <label for="first_name" class="col-sm-3 col-form-label">First name:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="first_name" placeholder="First name" name="first_name" value="{{$userDetail->first_name}}">
                                    </div>
                                </div>
                                @if(getCurrentRouteName() == 'admin.users.worker-detail')
                                    <div class="form-group row">
                                        <label for="last_name" class="col-sm-3 col-form-label">Last name:</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="last_name" placeholder="Last name" name="last_name" value="{{$userDetail->last_name}}">
                                        </div>
                                    </div>
                                @endif
                                <div class="form-group row">
                                    <label for="last_name" class="col-sm-3 col-form-label">Gender:</label>
                                    <div class="col-sm-9">
                                        <select id="" class="form-control custom-select" name="gender">
                                            <option value="m" {{$userDetail->gender == 'Male' ? 'selected' : ''}}>Male</option>
                                            <option value="f" {{$userDetail->gender == 'Female' ? 'selected' : ''}}>Female</option>
                                        </select>
                                    </div>
                                </div>
                                @if(getCurrentRouteName() == 'admin.users.worker-detail')
                                    <div class="form-group row">
                                        <label for="address" class="col-sm-3 col-form-label">Address:</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="address" placeholder="Address" name="address" value="{{$userDetail->address}}">
                                        </div>
                                    </div>
                                @endif
                                <div class="form-group row">
                                    <div class="col-sm-12 text-center">
                                        <button type="submit" class="btn btn-success btn_update_profile js_update_profile" data-url="{{route('admin.users.update', ['user' => $userDetail->id])}}">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-content -->
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- END Profile -->
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>