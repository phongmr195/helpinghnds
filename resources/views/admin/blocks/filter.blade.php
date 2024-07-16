
<!-- Row Filter -->
<div class="row mgb-15">
    @if(isset($block) && $block->type == 'form')
    <div class="{{(isset($component_name) && count($component_names) > 4) ? 'col-sm-12' : "col-sm-10"}}">
        <form class="{{\Str::slug($block->name)}}" id="{{\Str::slug($block->name)}}" action="{{route('admin.users.filter-customer')}}" method="GET">
            <div class="row">
                @if(isset($component_names) && count($component_names))
                    @foreach ($component_names as $item)
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="input{{$item->value}}" class="label-{{$item->value}}">{{$item->name}}</label>
                                @include('admin.components.filters.filter-' . \Str::slug($item->value))
                            </div>
                        </div>
                    @endforeach
                @endif
                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="inputSubmit" class="label-submit"></label>
                        @include('admin.components.filters.filter-submit')
                    </div>
                </div>
            </div>
        </form>
    </div>
    @endif
</div>
<!-- END Row Filter -->
