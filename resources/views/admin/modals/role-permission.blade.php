<div class="modal fade" id="modal-role-permission" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Decentralization</h4>
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
                            <form class="form-horizontal form_role_permission js_fr_role_permission" action="" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label> Select department</label>
                                        <select class="form-control custom-select js_wrap_option_role js_change_role" name="role_data">
                                            @foreach ($roles as $role)
                                                <option value="{{$role->id .'_'. $role->name}}">{{$role->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-6 flex-item-bottom">
                                        <button class="btn btn-success custom-btn js_show_modal_create_role">Add new department</button>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <div class="wrap-pages js-wrap-pages">
                                            <table class="table table-bordered table-sm data-list-qrcode-scroll">
                                                <thead>
                                                    <tr role="row">
                                                        <th>Page name</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($pages as $page)
                                                        <tr>
                                                            <td>{{$page->name}}</td>
                                                            <td>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" value="{{$page->id}}" name="page_ids[]" {{in_array($page->id, $rolePages) ? 'checked' : ''}}>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>    
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12 text-center">
                                        <button type="submit" class="btn btn-success js_setting_role_permission"><i class="far fa-check-circle"></i> Save</button>
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