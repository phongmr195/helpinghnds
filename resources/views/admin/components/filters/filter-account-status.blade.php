<select id="inputStatus" class="form-control custom-select" name="status">
    @foreach (config('constant.user_account_status') as $key => $value)
        <option value="{{$key}}" {{(isset(request()->status) && request()->status == $key) ? 'selected' : ''}}>{{$value}}</option>
    @endforeach
</select>