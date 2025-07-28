<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('lang.certificate') }}</title>
    <style>
        @font-face {
            font-family: 'lucidacalligraphyefregular';
            src: url('{{ public_path("assets/certificate-assets/fonts/lucida_calligraphy_font-webfont.ttf") }}');
        }
    </style>
    <link rel="stylesheet" href="{{asset('assets/certificate-assets/css/style.css')}}">
</head>
<body>
    <div class="print-area">
        <img src="{{public_path('assets/certificate-assets/images/logo.jpg')}}" class="print-logo"/>
        <img src="{{public_path('assets/certificate-assets/images/text.jpg')}}" class="print-text"/>
        <p class="course-title"><b>{{$pathname}}</b></p>


        <table style="padding-left: 100px; padding-right: 0px; margin-top: -100px;">
            <tr>
                <td style="padding-left: 50px; padding-right: 0px;">
                    <div class="learner-name" style="text-align: center;"><h4>{{$learnername}}</h4></div>
                    <div class="border" ></div>
                    <div style="font-size: 15px; margin-top: -120px; text-align: justify;">
                        <h2 style="margin-top: -15px; text-align: justify; text-align: center;">This certificate has been issued for successful completion of a Piaggio training course.</h2>
                    </div>
                </td>
                <td>
                    <img src="{{public_path('assets/certificate-assets/images/badge.png')}}" class="badge" width="140px" style="margin-top: 50px; padding-left: 20px;">
                </td>
            </tr>
        </table>
    </div>
</body>
</html>