<div class="modal fade" id="modal-create-user-account" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add new account</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Profile -->
                <div class="card">
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <form class="form-horizontal form_profile_information js_fr_create_account" action="" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <label for="password" class="col-sm-4 col-form-label">Password | Code: <span class="field_require">(*)</span></label>
                                    <div class="col-sm-8">
                                        @include('admin.components.inputs.password')
                                        <span class="help-block password-error"></span>
                                    </div>
                                </div>
                                <hr class="custom_hr">
                                <div class="form-group row mt-5">
                                    <label for="name" class="col-sm-4 col-form-label">Fullname: <span class="field_require">(*)</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="Enter fullname" name="name" autocomplete="off">
                                        <span class="help-block name-error"></span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="email" class="col-sm-4 col-form-label">Email: <span class="field_require">(*)</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="Enter email" name="email" autocomplete="off">
                                        <span class="help-block email-error"></span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="phone" class="col-sm-4 col-form-label">Phone: <span class="field_require">(*)</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="Enter phone" name="phone" autocomplete="off">
                                        <span class="help-block phone-error"></span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="group" class="col-sm-4 col-form-label">Gender:</label>
                                    <div class="col-sm-8">
                                        <select class="form-control custom-select" name="gender">
                                            <option value="m">Male</option>
                                            <option value="f">Female</option>
                                    </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="group" class="col-sm-4 col-form-label">Department:</label>
                                    <div class="col-sm-8">
                                        <select class="form-control js_wrap_option_role custom-select" name="role_data">
                                            @foreach ($roles as $role)
                                                <option value="{{$role->id . '_' .$role->name}}">{{$role->name}}</option>
                                            @endforeach
                                        </select>
                                        <span class="help-block role_data-error"></span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="status" class="col-sm-4 col-form-label">Status:</label>
                                    <div class="col-sm-8">
                                        <select class="form-control custom-select" name="status">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12 text-center">
                                        <button type="submit" class="btn btn-success js_create_user_account"><i class="fas fa-plus"></i> Add new</button>
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