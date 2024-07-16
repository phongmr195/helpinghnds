@if(isset($item))
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
@endif