<!DOCTYPE html>
<html>
<head>
    <title>{{ __('lang.congratulations') }}!</title>
</head>
<body>
    <h1>{{ $details['title'] }}</h1>
    <p>Dear <span style="font-weight:bold;font-style: italic;">{{ $details['name'] }}</span>!,</p>
   	 <p>{{ $details['body'] }}</p>
    <div>
    	<span>{{ __('lang.thank-you') }}</span>
    </div>
</body>
</html>