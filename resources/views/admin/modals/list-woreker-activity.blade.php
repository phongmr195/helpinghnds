<div class="modal fade" id="modal-list-worker-activity" style="display: none;" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    Earnings detail - <span class="text-uppercase custom-text-upercase"> {{$userDetail->name}} </span>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body pd0">
                <div class="card wrap-earnings-information mgb">
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- /.tab-pane -->
                            <form class="js-fr-filter js_form_filter_earnings_popup" method="GET">
                                <input type="hidden" name="user_id" value="{{$userDetail->id}}">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label for="">From date</label>
                                        <input type="text" name="from_date" class="form-control js_single_date" autocomplete="off" placeholder="MM-DD-YYYY" value="">
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="">To date</label>
                                        <input type="text" name="to_date" class="form-control js_single_date" autocomplete="off" placeholder="MM-DD-YYYY" value="">
                                    </div>
                                    <div class="col-sm-4 flex-item-bottom mt-mb-15">
                                        <button type="submit" class="btn btn-success js_filter_datatable">Search</button>
                                        <a href="javascript:void(0)" class="btn btn-warning js-refresh-list-worker-activity" style="margin-left: 5px">
                                            Refresh <i class="fas fa-redo-alt"></i>
                                        </a>
                                    </div>
                                </div>
                            </form>
                            <!-- /.tab-pane -->
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="total-earninged popup bg-warning text-center">
                                        <p class="text-center">
                                            Total earned <br/>
                                            <strong class="total-number js_show_total_earninged"></strong>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="total-earninged popup bg-warning text-center">
                                        <p class="text-center">
                                            Total earned <br/>
                                            <strong class="total-number js_show_total_cash_out"></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
    
                            <div class="row">
                                <div class="col-sm-12">
                                    <h6 class="js_show_text_activity">All activity history of worker</h6>
                                    <div class="table-responsive">
                                        <table id="table-earnings" class="table table-bordered table-hover dataTable dtr-inline table-sm" role="grid" aria-describedby="example2_info">
                                            <thead>
                                                <tr role="row">
                                                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending">Order ID</th>
                                                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending">Service</th>
                                                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Date</th>
                                                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Duration (hour)</th>
                                                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Fee (đ) / 1 hour</th>
                                                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Amount (đ)</th>
                                                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Tip (đ)</th>
                                                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Fee app (đ)</th>
                                                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Total earned (đ)</th>
                                                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Action</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-content -->
                    </div>
                    <!-- /.card-body -->
                </div>    
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>