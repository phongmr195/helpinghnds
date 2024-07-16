<select id="inputRating" class="form-control custom-select js_select_rating" name="rating">
    @foreach (config('constant.user_rating') as $key => $value)
        <option value="{{$key}}" {{(isset(request()->rating) && request()->rating == $key) ? 'selected' : ''}}>{{$value}}</option>
    @endforeach
</select>