<div class="modal fade modal-reset-password" id="grid-modal-reset-pass" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Thay đổi mật khẩu</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="crad">
                    <div class="form-group row mgb-0">
                        <div class="col-sm-8 form-group">
                            <input type="text" class="form-control js_pw_value" id="password" placeholder="Mật khẩu" name="password" value="">
                            <span class="pw-reset-validate js_validate_pw_reset">
                                Mật khẩu không được để trống
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
                                Mật khẩu random: <span class="js_show_pw_random text-bold"></span>
                            </p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12 text-center">
                            <button type="button" class="btn btn-success js_reset_password" data-url="">Cập nhật mật khẩu</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>