<!DOCTYPE html>
<html>
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="base-route" content="{{ url('/') }}">

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<body>
    <div id="app" class="container">
    	<user></user>
    </div>
    
    <script src="/js/app.js"></script>
</body>
</html>