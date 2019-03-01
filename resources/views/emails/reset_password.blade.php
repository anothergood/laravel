
<!DOCTYPE html>
<html>
  <head>
    <title>Reset Password Email</title>
  </head>
  <body>
    <h2>Welcome!</h2>
    <br/>
    To reset your password click on the link below to verify your email account
    <br/>
    <a href="{{url('welcome?token='.$token)}}">Verify Email</a>
  </body>
</html>
