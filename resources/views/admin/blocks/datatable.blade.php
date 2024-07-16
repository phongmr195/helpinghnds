@if(isset($datatable))
    <div class="row"> 
        <div class="col-sm-12">
            <table id="table-data" class="table table-bordered table-hover dataTable dtr-inline" role="grid" aria-describedby="example2_info">
                <thead>
                    <tr role="row">
                        <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending">Fullname</th>
                        @if(isset($component_names) && count($component_names))
                            @foreach ($component_names as $component)
                                <th class="sorting text-center" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending">{{$component->name}}</th> 
                            @endforeach    
                        @endif
                        <th class="sorting text-center" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($datatable as $item)
                        <tr class="odd">
                            <td>
                                <div class="info">
                                    <div class="user-avatar">
                                        @include('admin.partials.user-avatar', ['item' => $item])
                                    </div>
                                    <div class="name-and-phone">
                                        <div class="name">
                                            <span>
                                                <b>{{$item->name}}</b>
                                            </span>
                                        </div>
                                        <div class="phone">
                                            <span>
                                                {{$item->phone}}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            @if(isset($component_names) && count($component_names))
                                @foreach ($component_names as $component)
                                    @include('admin.components.datatables.data-' . \Str::slug($component->value))  
                                @endforeach    
                            @endif
                            <td class="text-center">
                                @include('admin.partials.user-action', ['item' => $item])
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif