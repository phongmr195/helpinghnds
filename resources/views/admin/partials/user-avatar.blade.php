@if($item)
    @if(!is_null($item->profile) && isImage($item->profile->avatar))
        <img class="img-avatar lozad" data-src="{{getPathImageUpload($item->profile->avatar)}}" alt="avatar">
    @else
        <i class="far fa-3x fa-user-circle"></i>
    @endif
@endif