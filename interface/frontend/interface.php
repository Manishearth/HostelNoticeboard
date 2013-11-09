<?php

$users = array(
    "User1" => "Password1",
    "User2" => "Password2",
);

if ($users[$_POST["user"]]!==$_POST["pass"]) {
	if ($_POST["attempt"]<5) header('Location: index.php?attempt='.$_POST["attempt"]);
	if ($_POST["attempt"]>4) header("HTTP/1.1 403 Unauthorized");
	exit();
}
echo '
<!DOCTYPE html>
<html>
<head>
  <title>IIT-Bombay Notice Board</title>
  <meta name=" viewport " content="width=device-width,initial-scale=1.0">
  <meta name="  description" content="File uploading interface for IIT-B Notice Board">
  <meta name="author" content="Kamal Galrani">
  <link rel="stylesheet" href="css/cyborg.css">
  <link rel="stylesheet" href="css/style.css">
  <link href="css/bootstrap-responsive.css" rel="stylesheet">
  <link href="css/tablecloth.css" rel="stylesheet">
</head>

<body>
  <div class="container">
    <div style="width:20%;padding:20px;">
      <ul class="nav nav-pills nav-stacked" style="top:30%">
      <li class="active" id="postLi"> <a id="postComplaintBtn"  title="">Action</a> </li>
      <li id="viewLi">  <a id="viewComplaintBtn" title="">Browse filesystem</a></li>
    </ul>
    </div>
    <div class="well" id="postComplaint" style="top:100px;">  
        <h2 style="text-align:center">IIT-B Notice Board</h2>
        <hr style="border:1px solid">
        <form action="action.php" method="post" class="form-horizontal">
            <div class="form-group">
              <div class="col-lg-3">
                <input type="hidden" name="user" value="'.$_POST["user"].'">
              </div>
            </div>
            <div class="form-group">  
                <label class="control-label col-lg-2"> Hostel No</label>
                <div class="col-lg-3 ">
                    <select class="form-control" id="sele" name="hostel">
                      <option value="0">All</option>
					  <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                      <option value="4">4</option>
                      <option value="5">5</option>
                      <option value="6">6</option>
                      <option value="7">7</option>
                      <option value="8">8</option>
                      <option value="9">9</option>
                      <option value="10">10</option>
                      <option value="15">10A</option>
                      <option value="11">11</option>
                      <option value="12">12</option>
                      <option value="13">13</option>
                      <option value="14">14</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
              <label class="control-label col-lg-2">Category</label>
              <div class="col-lg-3">
                <select name="category" class="form-control select-picker" id="category">
                  <option value="Academics">Academics</option>
                  <option value="Cultural">Cultural</option>
                  <option value="Sports">Sports</option>
                  <option value="Technical">Technical</option>
                  <option value="Hostel">Hostel</option>
				  <option value="Other">Other</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-lg-2">Directive</label>
              <div class="col-lg-3">
                <select name="category" class="form-control select-picker" id="category">
                  <option value="Academics">Upload</option>
                  <option value="Cultural">Remove</option>
                  <option value="Sports">Make Directory</option>
                </select>
              </div>
            </div>			
            <div class="form-group">
              <label class="control-label col-lg-2">File name</label>
              <div class="col-lg-6">
                <input type="text" name="path" value="" required="true" placeholder="" class="form-control">
              </div>
            </div>
            <div class="form-group">
              <div class="col-lg-10 col-lg-offset-2">
                <button type="submit" class="btn btn-primary" name="submitComplaint">Submit</button>
              </div>
            </div>
			<span class="help-block">
				For complaints and suggestions click <a href="#">here</a> <br>
			</span>
        </form> 
        <hr style="border:0.1px solid"> 
    </div> 
    <div class="well" id="viewComplaint">
	  <h2 style="text-align:center">IIT-B Notice Board</h2>
      <hr style="border:1px solid">
      <table cellspacing="3" cellpadding="3">
        
        <thead>
          <tr>
            <th>Date registered</th> <th>Category</th> <th>Directive</th> <th>Hostel</th> <th>File name</th>
          </tr>
        </thead>
        <tbody>
         
        </tbody>
      </table>
    </div>
  </div> 
    <script src="js/jquery-1.7.2.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="js/jquery.metadata.js"></script>
    <script src="js/jquery.tablesorter.min.js"></script>
    <script src="js/jquery.tablecloth.js"></script>
	<script src="js/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
          $("table").tablecloth({
          theme: "dark",
          striped: true,
          sortable: true,
          condensed: true
          });
          $("#viewComplaintBtn").click(function() {
            /* Act on the event */
            $("#postComplaint").fadeOut("slow");
            $("li").removeClass("active");
            $("#viewLi").addClass("active");
            $("#viewComplaint").fadeIn("slow");
          });
          $("#postComplaintBtn").click(function() {
            /* Act on the event */
            $("#postComplaint").fadeIn("slow");
            $("li").removeClass("active");
            $("#postLi").addClass("active");
            $("#viewComplaint").fadeOut("slow");
          });
        });
    </script>
</body>
</html>'

?>

