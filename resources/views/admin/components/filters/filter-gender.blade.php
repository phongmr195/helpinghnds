<select id="inputGender" class="form-control custom-select" name="gender">
    @foreach (config('constant.user_gender') as $key => $value)
        <option value="{{$key}}" {{(isset(request()->gender) && request()->gender == $key) ? 'selected' : ''}}>{{$value}}</option>
    @endforeach
</select>