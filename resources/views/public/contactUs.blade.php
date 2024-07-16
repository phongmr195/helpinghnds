<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact us</title>
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        .wrap-form-contact {
            display: block;
            max-width: 600px;
            margin: 20px auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="wrap-form-contact">
                    <form class="js_form_contact_us">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="">Title</label>
                                <input type="text" class="form-control" name="title" id="" placeholder="Enter title" required>
                            </div>
                            <div class="form-group">
                                <label for="">Content</label>
                                <textarea class="form-control" rows="5" name="content" placeholder="Enter message here..." required></textarea>
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-success js_btn_submit_contact">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    // Set the options that I want
    toastr.options = {
        "closeButton": true,
        "newestOnTop": true,
        "progressBar": true,
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "3000",
        "timeOut": "5000",
        "extendedTimeOut": "3000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
    $(document).ready(function () {
        $('.js_form_contact_us').on('submit', function (e){
            var url = "{{route('users.mail_contact_us')}}";
            var accessToken = "{{request()->accessToken}}";
            e.preventDefault();
            var btnSubmit = $('.js_btn_submit_contact');
            btnSubmit.text('Processing...');
            btnSubmit.attr('disabled', true);
            var formData = new FormData($('.js_form_contact_us')[0]);
            formData.append('accessToken', accessToken);
            $.ajax({
                url : url,
                method : 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res){
                    btnSubmit.text('Submit');
                    btnSubmit.attr('disabled', false);
                    toastr.success("Contact sent successfully!");
                    $('.js_form_contact_us').trigger('reset');
                },
                error: function(err){
                    btnSubmit.text('Submit');
                    btnSubmit.attr('disabled', false);
                }
            });
        })
    });
</script>
</html>