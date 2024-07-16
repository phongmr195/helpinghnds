@if($order->worker())
    <div class="info">
        <div class="user-avatar">
            @if(!is_null($order->worker->profile) && !empty($order->worker->profile->avatar))
                <img class="img-avatar" src="{{asset('/assets/images/customer-default.png')}}" alt="avatar">
            @else
                <i class="far fa-3x fa-user-circle"></i>
            @endif
        </div>
        <div class="name-and-phone">
            <div class="name">
                <a href="{{route('admin.users.worker-detail', ['user' => $order->worker->id])}}">
                    <span>
                        <b>{{$order->worker->name}}</b>
                    </span>
                </a>
            </div>
            <div class="phone">
                <span>
                    {{$order->worker->phone}}
                </span>
            </div>
        </div>
    </div>
@endif