<?php
$users = array(
    "User1" => "Password1",
    "User2" => "Password2",
);
if ( isset($_COOKIE["user"]) && isset($_COOKIE["auth"]) && md5($users[$_COOKIE["user"]])==$_COOKIE["auth"] ) {
   setcookie("user",$_COOKIE["user"],time()+900);
   setcookie("auth",$_COOKIE["auth"],time()+600);
}
elseif ((isset($_POST["user"]))&&(isset($_POST["pass"]))&&($users[$_POST["user"]]==$_POST["pass"])) {
   $auth = $_POST["pass"];
   setcookie("user",$_POST["user"],time()+900);
   setcookie("auth",md5($auth),time()+600);
}
else {
  setcookie("auth", "", time()-3600);
  if (!isset($_POST["attempt"])) header('Location: index.php');
  else if ($_POST["attempt"]<3) header('Location: index.php?attempt='.$_POST["attempt"]);
  else header("HTTP/1.1 403 Unauthorized");
  exit();
}

echo '
<!DOCTYPE html>
<html>
<head>
  <title>IIT-Bombay Notice Board</title>
  <meta name="viewport " content="width=device-width,initial-scale=1.0">
  <meta name="description" content="File uploading interface for IIT-B Notice Board">
  <meta name="author" content="Kamal Galrani">
  <link rel="stylesheet" href="css/cyborg.css">
  <link rel="stylesheet" href="css/style.css">
  <link href="css/bootstrap-responsive.css" rel="stylesheet">
  <link href="css/tablecloth.css" rel="stylesheet">
  <script src="js/jquery-1.7.2.min.js" type="text/javascript" charset="utf-8"></script>
  <script src="js/jquery.metadata.js"></script>
  <script src="js/jquery.tablesorter.min.js"></script>
  <script src="js/jquery.tablecloth.js"></script>
  <script src="js/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
  <script type="text/javascript" charset="utf-8">
      function hideFileInputs() {
        $("#task").children("option").each(function(){$(("[id^=".concat($(this).get(0).value)).concat("]")).hide();})
      }
      function hideAllFiles() {
        $("#category").children("option").each(function(){$(("[id^=".concat($(this).text())).concat("]")).hide();})
        $("#hostel").children("option").each(function(){$(("[id^=".concat($(this).text())).concat("]")).hide();})
      }
      function task_onChange() {
          hideFileInputs();
          $("#".concat($("#task option:selected").get(0).value)).show();
      }
      function category_onChange() {
          hideAllFiles();
          if ($("#category option:selected").text()=="Hostel") {
            $("#div_hostel").fadeIn("fast");
            $(("[id^=".concat($("#hostel option:selected").text())).concat("]")).show();
          }
          else {
            $("#div_hostel").fadeOut("fast");
            $(("[id^=".concat($("#category option:selected").text())).concat("]")).show();
            }
      }
      function hostel_onChange() {
          hideAllFiles();
          $(("[id^=".concat($("#hostel option:selected").text())).concat("]")).show();
      }
      function submit_onClick() {
        //Confirm Inputs
      }
      $(document).ready(function() {
        hideFileInputs();
        hideAllFiles();
        task_onChange();
        category_onChange();

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
</head>

<body>
  <div class="container">
    <div style="width:20%;padding:20px;">
      <ul class="nav nav-pills nav-stacked" style="top:30%">
        <li id="postLi" class="active"> <a id="postComplaintBtn"  title="">Add Task</a> </li>
        <li id="viewLi">  <a id="viewComplaintBtn" title="">Show History</a></li>
        <li id="browseLi">  <a id="browseComplaintBtn" href="root/">Browse Files</a></li>
      </ul>
    </div>
    <div class="well" id="postComplaint" style="top:100px;">  
        <h2 style="text-align:center">IIT-B Notice Board</h2>
        <hr style="border:1px solid">
        <form action="action.php" method="post" class="form-horizontal">
            <div class="form-group">
              <label class="control-label col-lg-2">Directive</label>
              <div class="col-lg-3">
                <select name="task" class="form-control select-picker" id="task" onChange="task_onChange()">
                  <option value="Copy">Upload File</option>
                  <option value="Delete">Remove File</option>
                  <option value="MkDir">Make Directory</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-lg-2">Parent Folder</label>
              <div class="col-lg-3">
              <select name="category" class="form-control select-picker" id="category" onChange="category_onChange()">';
//PHP
echo '            <option value="Academics">Academics</option>
                  <option value="Cultural">Cultural</option>
                  <option value="Sports">Sports</option>
                  <option value="Technical">Technical</option>
                  <option value="Hostel">Hostel</option>
                  <option id="root" value="Root">Root</option>
                </select>
              </div>
            </div>
            <div id="div_hostel" class="form-group">  
                <label class="control-label col-lg-2">Hostel No</label>
                <div class="col-lg-3 ">
                <select name="hostel" class="form-control select-picker" id="hostel" onChange="hostel_onChange()">';
//PHP
echo '                <option value="0">All</option>
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
                      <option value="11">11</option>
                      <option value="12">12</option>
                      <option value="13">13</option>
                      <option value="14">14</option>
                      <option value="15">15</option>
                    </select>
                </div>
            </div>			
            <div class="form-group" id="div_path">
              <label class="control-label col-lg-2">File/Folder</label>
              <div class="col-lg-3" id="Delete">
                <select name="Delete" class="form-control select-picker">
                  <option value="0">Select File</option>';
//PHP
echo '            <option value="1" id="Academics">File 1A</option>
                  <option value="2" id="Academics">File 2A</option>
                  <option value="3" id="Cultural">File 1C</option>
                  <option value="4" id="Sports">File 1S</option>
                  <option value="5" id="Sports">File 2S</option>
                  <option value="6" id="Technical">File 1T</option>
                  <option value="7" id="All">File 1H0</option>
                  <option value="8" id="All">File 1H0</option>
                  <option value="9" id="1">File 1H1</option>
                  <option value="10" id="2">File 1H2</option>
                  <option value="11" id="6">File 1H6</option>
                  <option value="12" id="6">File 2H6</option>
                  <option value="13" id="6">File 3H6</option>
                </select>
              </div>
              <div class="col-lg-3" id="MkDir">
                <input type="text" name="MkDir" value="" placeholder="" class="form-control">
              </div>
              <div class="col-lg-6" id="Copy">
                <input type="file" name="Copy" style="padding: 8px 1px">
              </div>
            </div>
            <div class="form-group">
              <div class="col-lg-10 col-lg-offset-2">
                <button type="submit" class="btn btn-primary" name="submit" onClick="submit_onClick()">Submit</button>
              </div>
            </div>
            <span class="help-block">For complaints and suggestions click <a href="#">here</a><br></span>
        </form> 
        <hr style="border:0.1px solid"> 
    </div> 
    <div class="well" id="viewComplaint">
      <h2 style="text-align:center">IIT-B Notice Board</h2>
      <hr style="border:1px solid">
      <table cellspacing="3" cellpadding="3">
        
        <thead>
          <tr>
            <th>Date registered</th> <th>Category</th> <th>Directive</th> <th>Hostel</th> <th>File name</th>';
//PHP
echo '    </tr>
        </thead>
        <tbody>
         
        </tbody>
      </table>
    </div>
  </div> 
</body>
</html>';

?>

