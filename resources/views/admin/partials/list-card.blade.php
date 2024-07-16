@if(isset($listCards) && count($listCards))
    @foreach ($listCards as $key => $card)
    @php
        $image = !is_null($card->img_url) ? $card->img_url : asset('/assets/images/atm-card.png');
    @endphp
        <div class="bank-item js_select_bank {{$key == 0 ? 'item-checked-bank' : ''}}" data-card-id="{{$card->id}}">
            <div class="bank-left-icon">
                <img src="{{$image}}" alt="">
            </div>
            <div class="bank-right-info">
                <p><b>{{$card->bank_name}}</b></p>
                <p>@include('admin.partials.card-hidden', ['cardItem' => $card])</p>
            </div>
        </div>
    @endforeach
@endif