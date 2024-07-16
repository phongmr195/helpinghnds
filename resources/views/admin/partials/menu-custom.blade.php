@php
    $menus = getPagesMenuLeft();
@endphp
@if(isset($menus) && count($menus))
    @foreach ($menus as $item)
        <li class="nav-item {{($item->children && count($item->children)) ? 'has-treeview' : ''}} {{$item->slug == 'report' ? 'menu-item-hidden' : ''}}">
            <a href="{{$item->route_name == 'admin.users.list' ? '#' : route($item->route_name)}}" class="nav-link {{createJsClassForMenuItem($item->slug)}} {{$item->slug == 'settings' ? 'custom-menu-item' : ''}}">
                <i class="{{config('constant.menu.fa_icons_class.' . $item->slug)}}"></i>
                <p>
                    {{$item->name}}
                    @if($item->children && count($item->children))
                        <i class="fas fa-angle-right right"></i>
                    @endif
                    @if($item->slug == 'cashout')
                        <span class="f-right js_show_cashout_waiting">
                            {!! createBadgeWaitingForCashout($item->slug) !!}
                        </span>
                    @endif
                </p>
            </a>
            @if($item->children && count($item->children))
                <ul class="nav nav-treeview js-nav-treeview">
                    @foreach ($item->children as $itemChilld)
                        <li class="nav-item">
                            <a href="{{route($itemChilld->route_name)}}" class="nav-link {{createJsClassForMenuItem($itemChilld->slug)}}">
                                <i class="{{config('constant.menu.fa_icons_class.' . $itemChilld->slug)}}"></i>
                                <p>{{$itemChilld->name}}</p>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </li>
    @endforeach
    <li class="nav-item">
        <a href="{{route('admin.logs_views')}}" class="nav-link js_logs_views_link">
            <i class="fa fa-cog"></i>
            <p>
                System logs
            </p>
        </a>
    </li>
@endif