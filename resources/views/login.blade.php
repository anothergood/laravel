<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
        </style>

        <!-- Latest compiled and minified CSS -->
    		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
    		integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    		<!-- Optional theme -->
    		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
    		integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

        <link rel="stylesheet" href="{{asset('css/app.css')}}">

        <script src="{{asset('js/app.js')}}" defer></script>

    </head>
    <body>
      <div id="app">
      <div class="container" style="padding-top: 20px; width: 40%">
        <form action="192.168.1.125/api/v1/user/login" method="POST" class="form-horizontal">
          <div class="row">
            <div class="col-md-2"><label>Введите email</label></div>
            <div class="col-md-10"><input class="form-control" type="email" name="email" placeholder="Введите email"></div>
          </div>
          <div class="row" style="padding-top: 10px;">
            <div class="col-md-2"><label>Введите пароль</label></div>
            <div class="col-md-10"><input class="form-control" type="password" name="password" placeholder="Введите пароль"></div>
          </div>
          <div class="row" style="padding-top: 10px;">
            <div class="col-md-12"><button type="submit" class="btn btn-success">Отправить</button></div>
          </div>
        </form>
        <example-component></example-component>
      </div>
      </div>
	   </body>
</html>
