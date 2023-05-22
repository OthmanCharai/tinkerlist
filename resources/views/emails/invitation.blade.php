<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Event {{$title}}</title>
</head>
<body>

<h1>
    @if(isset($emailTYpe))

        Event {{$title}} Has been Updated
    @else
        Welcome to our Event {{$title}}
    @endif
</h1>
<div style="display: flex">
    <div style="width: 30%;height: 30%">
        Location: {{$location}}
    </div>
    <div style="width: 30%;height: 30%">
        Date: {{$date}}
    </div>
    <div style="width: 30%;height: 30%">
        Time: {{$time}}
    </div>

</div>

</body>
</html>
