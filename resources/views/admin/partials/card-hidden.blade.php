@if(isset($cardItem))
    @php
        $lenghtCard = strlen($cardItem->bank_no);
        $num = $lenghtCard >= 12 ? 4 : 2;
        $lenghtX = strlen(substr($cardItem->bank_no, $num, $lenghtCard - $num));
        $cardX = '';
        for ($i = $num; $i < $lenghtX; $i++) {
            $cardX.='*';
        }
    @endphp
    {{substr($cardItem->bank_no, 0, $num) .$cardX . substr($cardItem->bank_no, $lenghtCard - $num, $lenghtCard)}}
@endif