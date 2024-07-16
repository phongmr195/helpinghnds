<div class="modal fade" id="modal-create-role" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add new department</h4>
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
                            <form class="form-horizontal" action="" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <label> Enter department name</label>
                                        <input class="form-control js_get_role_name" type="text" placeholder="Enter department name" name="role_name">
                                        <span class="help-block role_name-error"></span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12 text-center">
                                        <button type="submit" class="btn btn-success js_create_role"><i class="fas fa-plus"></i> Add new</button>
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