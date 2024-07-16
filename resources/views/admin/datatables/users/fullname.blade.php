@if($user)
    <div class="info">
        <div class="user-avatar">
            @if(!is_null($user->profile) && isImage($user->profile->avatar))
                <img class="img-avatar" src="{{getPathImageUpload($user->profile->avatar)}}" alt="avatar">
            @else
                <i class="far fa-3x fa-user-circle"></i>
            @endif
        </div>
        <div class="name-and-phone">
            <div class="name">
                <span>
                    <b>{{$user->name}}</b>
                </span>
            </div>
            <div class="phone">
                <span>
                    {{$user->phone}}
                </span>
            </div>
        </div>
    </div>
@endif