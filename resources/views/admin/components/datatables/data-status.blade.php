@if(isset($item))
    <td class="text-center">
        @if($item->status)
            <span class="user_published">
                <i class="far fa-check-circle"></i>
            </span>
        @else
            <span class="user_unpublished">
                <i class="far fa-times-circle"></i>
            </span>
        @endif
    </td>
@endif

