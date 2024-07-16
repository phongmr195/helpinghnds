<div class="modal fade modal-reset-password" id="modal-reset-password" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Change password</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="crad">
                    <div class="form-group row mgb-0">
                        <div class="col-sm-8 form-group">
                            <input type="text" class="form-control js_pw_value" id="password" placeholder="Password" name="password" value="">
                            <span class="pw-reset-validate js_validate_pw_reset">
                                Password is required
                            </span>
                        </div>
                        <div class="col-sm-4">
                            <button type="button" class="btn btn-sm btn-info form-control text-bold js_random_number">
                                Random
                            </button>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <p>
                                Password is random: <span class="js_show_pw_random text-bold"></span>
                            </p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12 text-center">
                            <button type="button" class="btn btn-success js_reset_password" data-url="{{route('admin.users.reset-password', ['user' => $userDetail->id])}}">Reset password</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>