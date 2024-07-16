@if($order->customer())
    <div class="info">
        <div class="user-avatar">
            @if(!is_null($order->customer->profile) && !empty($order->customer->profile->avatar))
                <img class="img-avatar" src="{{asset('/assets/images/customer-default.png')}}" alt="avatar">
            @else
                <i class="far fa-3x fa-user-circle"></i>
            @endif
        </div>
        <div class="name-and-phone">
            <div class="name">
                <a href="{{route('admin.users.customer-detail', ['user' => $order->customer->id])}}">
                    <span>
                        <b>{{$order->customer->name}}</b>
                    </span>
                </a>
            </div>
            <div class="phone">
                <span>
                    {{$order->customer->phone}}
                </span>
            </div>
        </div>
    </div>
@endif