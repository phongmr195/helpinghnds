@if(isset($roles))
    @foreach ($roles as $role)
        <option value="{{$role->id .'_'. $role->name}}">{{$role->name}}</option>
    @endforeach
@endif