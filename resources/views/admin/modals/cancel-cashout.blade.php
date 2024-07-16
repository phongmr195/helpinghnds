<div class="modal fade modal-update-status wrap-content-detail" id="modal-cancel-cashout" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Cancel cashout</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Status -->
                <div class="card">
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <form action="" method="POST" class="js_form_cancel_cashout">
                                @csrf
                                <input type="hidden" name="cashout_id" value="" class="js_set_cashout_id">
                                <div class="row">
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control js_get_reason" name="reason" placeholder="Enter reason">
                                    </div>
                                    <div class="col-sm-4 mt-mb-15">
                                        <button type="button" class="btn btn-sm btn-success form-control js_submit_cancel_cashout" data-url="{{route('admin.ajax.cancel_cashout')}}">
                                            Submit
                                        </button>
                                    </div>
                                </div>
                                <span class="help-block reason-error"></span>
                            </form>
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