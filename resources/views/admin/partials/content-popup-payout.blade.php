<input type="hidden" value="{{$card->id}}" name="card_id_info">
<input type="hidden" value="{{$workerID}}" name="user_id_info">
<input type="hidden" value="{{$payoutMoney}}" name="cash_out_amount_info">
<p class="text-center"><b>{{number_format($payoutMoney, 0, ',', '.')}} đ</b></p>
<p class="text-center">Rút tiền về tài khoản đã xác nhận</p>
<div class="bank-info">
    <p>Ngân hàng: <span>{{$card->code}}</span> </p>
    <p>Số tài khoản:
    <span>
        @include('admin.partials.card-hidden', ['cardItem' => $card])
    </span>
    </p>
    <p>Tên người thụ hường: <span>{{$card->fullname}}</span></p>
    <div class="form-group">
        <label>Xác nhận mật khẩu</label>
        <input type="password" name="password" class="form-control" placeholder="Mật khẩu đăng nhập ứng dụng">
        <span class="password-error"></span>
    </div>
</div>
<div class="note">
    <p><b>Lưu ý:</b></p>
    <p>Giao dịch sẽ duyệt và hoàn tất trong vòng 3 ngày làm việc.</p>
</div>
<div class="form-group row">
    <div class="col-sm-12 text-center">
        <button type="submit" class="btn btn-success js_btn_confirm_cashout">Xác nhận giao dịch</button>
    </div>
</div>