<!DOCTYPE html>
<html>
<head>
    <title>Follower Mail</title>
</head>
<body>
    
   	<div class="row">
       A new reply has been added to the thread which you are following: 
       <b> {!!  $data->thread->title !!} </b>
    </div>
    <b> New comment:</b>
    <p> {!! $reply !!}</p>
    <div>
    	<span>Best Regards</span>
        <br>
        The ReallyLesson Team
    </div>
</body>
</html>