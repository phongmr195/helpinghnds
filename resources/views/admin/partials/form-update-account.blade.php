@if(isset($userDetail))
    <form class="form-horizontal form_profile_information js_fr_profile_account" action="" method="POST">
        @csrf
        <div class="form-group row">
            <label for="name" class="col-sm-3 col-form-label">Fullname:</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="fullname" placeholder="Enter fullname" name="name" value="{{$userDetail->name}}" autocomplete="off">
                <span class="help-block name-error"></span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Department:</label>
            <div class="col-sm-9">
                <select id="" class="form-control custom-select" name="role_data">
                    @foreach ($roles as $role)
                        <option value="{{$role->id .'_'. $role->name}}" {{($userDetail->role_id == $role->id) ? 'selected' : ''}}>{{$role->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label for="last_name" class="col-sm-3 col-form-label">Gender:</label>
            <div class="col-sm-9">
                <select id="" class="form-control custom-select" name="gender">
                    @foreach (config('constant.user_select_gender') as $key => $value)
                        <option value="{{$key}}" {{($userDetail->gender == $value) ? 'selected' : ''}}>{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label for="last_name" class="col-sm-3 col-form-label">Status :</label>
            <div class="col-sm-9">
                <select id="" class="form-control custom-select" name="status">
                    @foreach (config('constant.user_status_select') as $key => $value)
                        <option value="{{$key}}" {{$userDetail->status == $key ? 'selected' : ''}}>{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-12 text-center">
                <button type="submit" class="btn btn-success btn_update_profile js_update_profile_account" data-url="{{route('admin.users.update-account', ['user' => $userDetail->id])}}">Update</button>
            </div>
        </div>
    </form>
@endif