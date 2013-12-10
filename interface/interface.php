<?php
define('READ_FILESYSTEM',		0);
define('WRITE_FILESYSTEM',		1);
define('DELETE_OTHER_USER_FILES',	2);
define('ADD_DELETE_USER',       	4);
define('ADD_DELETE_PI',	       		8);

include 'backend/MySQL_frontend.class.php';
include 'backend/config.inc';

$dbLink=new MySQL($dbUsername,$dbPassword);

//echo $dbLink->getAuth("kamal1210");
//exit();


if ( isset($_COOKIE["user"]) && isset($_COOKIE["auth"]) && $dbLink->getAuth($_COOKIE["user"])==$_COOKIE["auth"] ) {
  setcookie("user",$_COOKIE["user"],time()+900);
  setcookie("auth",$_COOKIE["auth"],time()+600);
  $user=$_COOKIE["user"];
}
elseif ((isset($_POST["user"]))&&(isset($_POST["pass"]))&&($dbLink->getAuth($_POST["user"])==$_POST["pass"])) {
  setcookie("user",$_POST["user"],time()+900);
  setcookie("auth",$_POST["pass"],time()+600);
  $user=$_POST["user"];
}
else {
  setcookie("auth", "", time()-3600);
  if (!isset($_POST["attempt"])) header('Location: index.php');
  else if ($_POST["attempt"]<3) header('Location: index.php?attempt='.$_POST["attempt"]);
  else header("HTTP/1.1 403 Unauthorized");
  exit();
}
?>
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
		$('[name=Delete] [value="0"]')[0].selected='selected'
        $("#category").children("option").each(function(){$(("[id^=".concat($(this).text())).concat("]")).hide();})
        $("#hostel").children("option").each(function(){$(("[id^=".concat($(this).text())).concat("]")).hide();})
      }
      function task_onChange() {
          hideFileInputs();
          var curtask=$("#task option:selected").val();
          $("[data-task^='task-']").hide()
          $("[data-task^='task-"+curtask+"'],[data-task$='task-"+curtask+"']").show();
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
        <form action="action.php" method="post" class="form-horizontal" enctype="multipart/form-data">
            <div class="form-group">
              <label class="control-label col-lg-2">Directive</label>
              <div class="col-lg-3">
                <select name="task" class="form-control select-picker" id="task" onChange="task_onChange()">
                  <option value="Copy">Upload File</option>
                  <option value="Delete">Remove File</option>
                  <?
echo "\n";
//if($dbLink->isAdmin($user))
//echo '                  <option value="MkDir">Make Directory</option>';
if($dbLink->hasPerm($user,ADD_DELETE_USER)) {
	echo '                  <option value="AddUser">Add User</option>
                  <option value="DelUser">Delete User</option>';
	echo "\n";
}
if($dbLink->hasPerm($user,ADD_DELETE_PI)) {
	echo '                  <option value="AddPI">Add PI</option>
                  <option value="DelPI">Delete PI</option>';
	echo "\n";
}
?> </select>
              </div>
            </div>
            <div class="form-group" id="div_parent" data-task="task-Copy task-Delete">
              <label class="control-label col-lg-2">Parent Folder</label>
              <div class="col-lg-3">
              <select name="category" class="form-control select-picker" id="category" onChange="category_onChange()">
                  <option value="Academics">Academics</option>
                  <option value="Cultural">Cultural</option>
                  <option value="Sports">Sports</option>
                  <option value="Technical">Technical</option>
                  <option value="Hostel">Hostel</option>
                  <?
echo "\n";
//if($dbLink->isAdmin($user)) {
//	echo '                  <option id="root" value="Root">Root</option>';
//	echo "\n";
//}
?></select>
              </div>
            </div>
            <div id="div_hostel" class="form-group">  
              <label class="control-label col-lg-2">Hostel No</label>
              <div class="col-lg-3 ">
              <select name="hostel" class="form-control select-picker" id="hostel" onChange="hostel_onChange()">
                <option value="0">All</option>
                <?
echo "\n";
$hostels = $dbLink->getHostels();
foreach ($hostels as &$hostel) {
	echo '                <option value="'.$hostel.'">'.$hostel.'</option>';	
	echo "\n";
}
?>   </select>
              </div>
            </div>			
            <div class="form-group" id="div_path" data-task="task-Copy task-Delete">
              <label class="control-label col-lg-2">File/Folder</label>
              <div class="col-lg-3" data-task="task-Delete">
                <select name="Delete" class="form-control select-picker">
                  <option value="0">Select File</option><?
echo "\n";
$_files = $dbLink->getFileList($path);
foreach ($_files[0] as &$folder) {
	foreach ($_files[$folder] as &$file) {
		if ($file == "." || $file == "..") continue;
		echo '                  <option value="'.$folder."/".$file.'" id="'.$folder.'">'.$file.'</option>';
		echo "\n";
	}
}
?> </select>
              </div>
<!--          <div class="col-lg-3" data-task="task-MkDir">
                <input type="text" name="MkDir" value="" placeholder="" class="form-control">
              </div>
-->
              <div class="col-lg-6" data-task="task-Copy">
                <input type="file" name="Copy" style="padding: 8px 1px">
              </div>
            </div>
            
            
            
            <div class="form-group" id="div_username" data-task="task-AddUser task-DelUser" style="display: block;">
              <label class="control-label col-lg-2">Username</label></label>
              <div class="col-lg-3">
              <input name="user-username" type="text" class="form-control"/>
              </div>
            </div>
             <div class="form-group" id="div_password1" data-task="task-AddUser" style="display: block;">
              <label class="control-label col-lg-2">Password</label></label>
              <div class="col-lg-3">
              <input name="user-password1" type="password" class="form-control"/>
              </div>
              
            </div>
              <div class="form-group" id="div_password2" data-task="task-AddUser" style="display: block;">
              <label class="control-label col-lg-2">Password again</label></label>
              <div class="col-lg-3">
              <input name="user-password2" type="password" class="form-control"/>
              </div>
              
            </div>           

             <div class="form-group" id="div_files" data-task="task-AddUser" style="display: block;">
              <label class="control-label col-lg-2">Upload files</label></label>
              <div class="col-lg-3">
              <input name="user-permission-upload" type="checkbox" class="form-control"/>
              </div>
              
            </div>  
            
             <div class="form-group" id="div_deletefiles" data-task="task-AddUser" style="display: block;">
              <label class="control-label col-lg-2">Delete all files</label></label>
              <div class="col-lg-3">
              <input name="user-permission-delete" type="checkbox" class="form-control"/>
              </div>
              
            </div>  
            
             <div class="form-group" id="div_permusers" data-task="task-AddUser" style="display: block;">
              <label class="control-label col-lg-2">Add/Delete users</label></label>
              <div class="col-lg-3">
              <input name="user-permission-users" type="checkbox" class="form-control"/>
              </div>
              
            </div>  
             <div class="form-group" id="div_permpis" data-task="task-AddUser" style="display: block;">
              <label class="control-label col-lg-2">Add/Delete Pis</label></label>
              <div class="col-lg-3">
              <input name="permission-pi" type="checkbox" class="form-control"/>
              </div>
              
            </div>  




            <div class="form-group" id="div_piip" data-task="task-AddPI" style="display: block;">
              <label class="control-label col-lg-2">IP</label></label>
              <div class="col-lg-3">
              <div class="input-group"><input name="pi-ip" type="text" class="form-control" value="10."/>
              <span class="input-group-btn"><input type=text class="form-control" size=2 value=22 name="pi-port"></span>
              </div>
              </div>
            </div>
             <div class="form-group" id="div_piuser" data-task="task-AddPI" style="display: block;">
              <label class="control-label col-lg-2">Username</label></label>
              <div class="col-lg-3">
              <input name="pi-pass" type="text" class="form-control"/>
              </div>
            </div>                 
             <div class="form-group" id="div_pipass" data-task="task-AddPI" style="display: block;">
              <label class="control-label col-lg-2">Password</label></label>
              <div class="col-lg-3">
              <input name="pi-pass" type="password" class="form-control"/>
              </div>
            </div>
                            
       <div id="div_pihostel" class="form-group" data-task="task-AddPI">  
              <label class="control-label col-lg-2">Hostel No</label>
              <div class="col-lg-3 ">
              <select name="pi-hostel" class="form-control select-picker" id="pi-hostel">
                <option value="0">None</option>
                <?
echo "\n";
$hostels = $dbLink->getHostels();
foreach ($hostels as &$hostel) {
	echo '                <option value="'.$hostel.'">'.$hostel.'</option>';	
	echo "\n";
}
?>   </select>
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
            <th>Date registered</th> <th>Category</th> <th>Directive</th> <th>Hostel</th> <th>File name</th>
            
            <?/*PHP*/?>
            
              </tr>
        </thead>
        <tbody>
         
        </tbody>
      </table>
    </div>
  </div> 
</body>
</html>

