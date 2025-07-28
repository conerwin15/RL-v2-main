<!DOCTYPE html>
<html>
<head>
    <title>Congratulations!</title>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p>Dear <span style="font-weight:bold;font-style: italic;">{{ $body }}</span>!,</p>
    <p> Please find the attached certificate file. </p>
   
    <div>
    	<span>Thank you,</span>
    	<br>
    	<span style="font-weight:bold;font-style: italic;">{{$team}}</span>
    </div>
</body>
</html>