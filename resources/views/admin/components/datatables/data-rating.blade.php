@if(isset($item))
    <td class="user-rating text-center">
        {!! showRatingStar($item->ratings_avg_rating) !!}
    </td>
@endif