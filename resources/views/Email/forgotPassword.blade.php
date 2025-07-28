<!DOCTYPE html>
<html>
<head>

    <title>Reset Password!</title>
</head>
<body>
    <h1>Reset Password</h1>
    <p><a class="btn btn-link"  href="{{url('/password?token=' . $token)}}"></a></p>
    <div>
    	<span>Thank you</span>
    </div>
</body>
</html>