<!DOCTYPE html>
<html>
<head>
  <title>IIT-Bombay Notice Board</title>
  <meta name=" viewport " content="width=device-width,initial-scale=1.0">
  <meta name="  description" content="File uploading interface for IIT-B Notice Board">
  <meta name="author" content="Kamal Galrani">
  <link rel="stylesheet" href="css/cyborg.css">
  <script src="js/jquery-1.7.2.min.js" type="text/javascript" charset="utf-8"></script>
  <script src="js/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
</head>
<body>
    <div class="well" style="width:50%;position:absolute;left:20%;top:200px;">  
        <h2>  IIT-B Notice Board Login</h2>
        <hr style="border:1px solid">
        <form action="interface.php" method="post" class="form-horizontal" accept-charset="utf-8">
			<input type="hidden" name="attempt" value=<?php if (isset($_GET['attempt'])) {printf("%d",$_GET['attempt']+1);} else printf("%d",1);?>>
            <div class="control-group">  
                <label class="control-label" for = "inputEmail"> Username</label>
                <div class="controls input-append">
                    <input type=" text" name="user" required="true" value="">
                </div>
            </div> 
            <div class="control-group">  
                <label class="control-label" for = "inputPassword"> Password</label>
                <div class="controls">
                    <input type="password" name="pass" required="true" value="">
                </div>
            </div> 
            <br>
             <div class="control-group">  
                <div class="controls">
                    <input type="submit" name="login" required="true" value="Login" class="btn btn-success">
                </div>
            </div>   
        </form>  
    </div>  
</body>
</html>