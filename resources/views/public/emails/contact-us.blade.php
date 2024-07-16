<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us Email</title>
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
    <style>
        .wrap-form-contact {
            display: block;
            max-width: 600px;
            margin: 70px auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <p>Đã có yêu cầu liên hệ từ ứng dụng Assist, vui lòng liên hệ lại theo thông tin dưới đây: </p>
                <p>Họ tên: {{$mail_data['name']}}</p>
                <p>Điện thoại: {{$mail_data['phone']}}</p>
                <p>Liên hệ về: {{$mail_data['title']}}</p>
                <p>Nội dung: {{$mail_data['content']}}</p>

                <div style="display: block; margin-top: 60px">
                    <p>Ngày gửi yêu cầu: {{date('d-m-Y H:i:s')}}</p>
                    <p>Ghi chú: Đây là email gửi từ hệ thống, không reply mail này.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>