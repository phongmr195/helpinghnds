<div class="modal fade modal-update-status wrap-content-detail" id="modal-update-status-{{$item->id}}" style="display: none;" aria-hidden="true">
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
                                    <select id="inputStatus" class="form-control custom-select js_select_status_{{$item->id}}" name="status">
                                        <option value="1" {{$item->status == 1 ? 'selected' : ''}}>Active</option>
                                        <option value="0" {{$item->status == 0 ? 'selected' : ''}}>Inactive</option>
                                        <option value="2" {{$item->status == 2 ? 'selected' : ''}}>Pending</option>
                                        <option value="3" {{$item->status == 3 ? 'selected' : ''}}>Rejected by admin</option>
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <button type="button" class="btn btn-sm btn-success form-control js_update_status" data-id="{{$item->id}}" data-url="{{route('admin.users.update-status', ['user' => $item->id])}}">
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