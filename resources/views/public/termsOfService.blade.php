<?php
//default en
$urlFile = 'https://drive.google.com/file/d/1wP0ugwV1_B4t7hYGR5CZulDGDYVkETHk/preview';
$urlViFile = 'https://drive.google.com/file/d/1ry9GpYjnERzz8Djn7LyELVPSosNNrfWa/preview';
if (!empty($lang) && $lang == 'vi') {
    $urlFile = $urlViFile;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of service</title>
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
        }

        iframe {
            height: 99vh;
            border: 0
        }
    </style>
    <script type="text/javascript">
        function _onchange(event) {
            var value = event.target.value;
            var url = '{{$urlFile}}';
            if (value == 'vi') {
                url = '{{$urlViFile}}';
            }
            document.getElementById('ifrm1').src = url;
            document.getElementById('txtLang1').innerHTML = value == 'vi' ? 'Ngôn ngữ: ' :'Language: ';
        };
    </script>
</head>

<body>
    <!-- <div style="text-align: center; padding: 8px 0;">
        <b id="txtLang1">Language: </b>
        <select onchange="_onchange(event)">
            <option value="en">English</option>
            <option value="vi">Tiếng Việt</option>
        </select>
    </div> -->
    <iframe id="ifrm1" src="{{$urlFile}}" width="100%" allow="autoplay"></iframe>
</body>

</html>