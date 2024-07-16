@if(isset($pages))
    <table class="table table-bordered table-sm data-list-qrcode-scroll">
        <thead>
            <tr role="row">
                <th>Permission name	</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pages as $page)
                <tr>
                    <td>{{$page->name}}</td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="{{$page->id}}" name="page_ids[]" {{(isset($rolePages) && in_array($page->id, $rolePages)) ? 'checked' : ''}}>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif