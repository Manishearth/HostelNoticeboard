<?php
define('UPLOAD_FILE',               1);
define('DELETE_FILE',               2);
define('ADD_DELETE_USER',           4);
define('ADD_DELETE_PI',             8);
define('APPROVE_UPLOAD',            16);
//define('CREATE_DIRECTORY',          32);                                      Not implemented
//define('DELETE_DIRECTORY',          64);                                      Not implemented
//define('ADDITIONAL_PERMISSION',     128);                                     Extra permission
//define('ADDITIONAL_PERMISSION',     256);                                     Extra permission
//define('ADDITIONAL_PERMISSION',     512);                                     Extra permission
//define('ADDITIONAL_PERMISSION',     1024);                                    Extra permission
define('ALLOW_HOSTEL',              2048);                                      //Not implemented
define('ALLOW_TECHNICAL',           4096);                                      //Not implemented
define('ALLOW_CULTURAL',            8192);                                      //Not implemented
define('ALLOW_SPORTS',              16384);                                     //Not implemented
define('ALLOW_ACADEMICS',           32768);                                     //Not implemented

include 'backend/MySQL.class.php';                                              //Import MySQL Class
include 'backend/config.inc';                                                   //Import some variables
include 'backend/functions.inc';                                                //Import some functions

$dbLink=new MySQL($dbUsername,$dbPassword);                                     //Starting new MySQL connection

//--------------Basic cookie based authentication------------//                 //To be improved later

if ((isset($_POST["user"]))&&(isset($_POST["pass"]))&&($dbLink->getAuth($_POST["user"])==md5($_POST["pass"]))) {
  setcookie("user",$_POST["user"],time()+900);
  setcookie("auth",md5($_POST["pass"]),time()+600);
  $user=$_POST["user"];
}
elseif (isset($_COOKIE["user"]) && isset($_COOKIE["auth"]) && $dbLink->getAuth($_COOKIE["user"])==$_COOKIE["auth"] ) {
  setcookie("user",$_COOKIE["user"],time()+900);
  setcookie("auth",$_COOKIE["auth"],time()+600);
  $user=$_COOKIE["user"];
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
    <meta name="keywords" content="IIT Bombay, Noticeboard"/>
    <meta name="description" content="Interface for IIT-Bombay Notice Board"/>
    <meta name="description" content="Interface for IIT-B Notice Board">
    <meta name="author" content="Manish Goregaonkar">
    <meta name="author" content="Kamal Galrani">

    <meta name="viewport " content="width=device-width,initial-scale=1.0">
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
        function task_onChange() {
            var currtask=$("#task option:selected").val();
            var datatasks=$("[filter^='task-']");
            datatasks.hide();
            var index;
            for (index = 0; index < datatasks.length; ++index) {
                if($(datatasks[index]).attr('filter').indexOf(currtask) > -1)
                    $(datatasks[index]).show();
            }
            $('#category').val("0");
            category_onChange();
        }
        function category_onChange() {
            $("[filter^='category-']").hide();
            $("[filter^='hostel-']").hide();
            if ($("#category option:selected").val()=="Hostel") {
                $("#div_hostel").show();
                $('#hostel').val("0");
                hostel_onChange();
            }
            else {
                $("#div_hostel").hide();
                $("[filter^=category-"+$("#category option:selected").val()+"]").show();
            }
        }
        function hostel_onChange() {
            $("[filter^='hostel-']").hide();
            $("[filter^=hostel-"+$("#hostel option:selected").val()+"]").show();
        }
        function submit_onClick() {
            //Confirm Inputs
        }

        $(document).ready(function() {
            $('#task').val("0");
            task_onChange();
            category_onChange();

            $("table").tablecloth({
              theme: "dark",
              striped: true,
              sortable: true,
              condensed: true
            });
            $("#page-history-btn").click(function() {
                $("#page-task").fadeOut("slow");
                $("#page-task-li").removeClass("active");
                $("#page-history-li").addClass("active");
                $("#page-history").fadeIn("slow");
            });
            $("#page-task-btn").click(function() {
                $("#page-history").fadeOut("slow");
                $("#page-history-li").removeClass("active");
                $("#page-task-li").addClass("active");
                $("#page-task").fadeIn("slow");
            });
        });
    </script>
</head>
<body>
<div class="container">
    <div style="width:20%; padding:20px;">
        <ul class="nav nav-pills nav-stacked" style="top:30%">
            <li id="page-task-li" class="active">   <a id="page-task-btn"  style="cursor: pointer;">Add Task</a> </li>
            <li id="page-history-li">               <a id="page-history-btn" style="cursor: pointer;">Show History</a></li>
            <li id="page-browse-li">                <a id="page-browse-btn" href="root/">Browse Files</a></li>
        </ul>
    </div>
    <!------------------Add Task Page-------------------->
    <div class="well" id="page-task" style="top:100px; width:70%; position:absolute; left:20%;">  
        <h2 style="text-align:center">IIT-B Notice Board</h2>
        <hr style="border:1px solid">
        <form action="action.php" method="post" class="form-horizontal" enctype="multipart/form-data">
        <!--------------Select Task---------------------->
        <div class="form-group" id="div_task">
            <label class="control-label col-lg-2">Task</label>
            <div class="col-lg-3">
                <select name="task" class="form-control select-picker" id="task" onChange="task_onChange()">
                    <option value="0" disabled selected>Select Task</option>
<?php
//--------------Show tasks as per permissions------------//
if($dbLink->hasPerm($user,UPLOAD_FILE)) {
    echo "                    <option value='upload'>Upload File</option>\n";
    echo "                    <option value='delete'>Delete File</option>\n";
}
if($dbLink->hasPerm($user,ADD_DELETE_USER)) {
    echo "                    <option value='add-user'>Add User</option>\n";
    echo "                    <option value='del-user'>Delete User</option>\n";
}
if($dbLink->hasPerm($user,ADD_DELETE_PI)) {
    echo "                    <option value='add-pi'>Add PI</option>\n";
    echo "                    <option value='del-pi'>Delete PI</option>\n";
}
//if($dbLink->hasPerm($user,CREATE_DIRECTORY))
//    echo "                    <option value='mkdir'>Make Directory</option>\n";
//if($dbLink->hasPerm($user,DELETE_DIRECTORY))
//    echo "                    <option value='rmdir'>Remove Directory</option>\n";
?> 
                </select>
            </div>
        </div>
        <!---------------Select category----------------->
        <div class="form-group" id="div_category" filter="task-upload task-delete task-mkdir task-rmdir">
            <label class="control-label col-lg-2">Category</label>
            <div class="col-lg-3">
                <select name="category" class="form-control select-picker" id="category" onChange="category_onChange()">
                    <option value="0" disabled selected>Select Category</option>
<?php
//-------------------Get list of categories-------------//
echo $path;
$_files = $dbLink->getFileList($path);
foreach ($_files[0] as &$folder)
    echo "                    <option value='$folder'>$folder</option>\n";
//if($dbLink->hasPerm($user,CREATE_DIRECTORY))
//    echo '                    <option value="." filter="task-mkdir task-rmdir">Root</option>'."\n";
?>
                </select>
            </div>
        </div>
        <!-----------Select hostel location------------->
        <div class="form-group" id="div_hostel" filter="task-">  
            <label class="control-label col-lg-2">Hostel/Location</label>
            <div class="col-lg-3 ">
                <select name="hostel" class="form-control select-picker" id="hostel" onChange="hostel_onChange()">
<!------------------<option value="0">All</option>------>
<?php
//--------------------Get list of hostels--------------//
$_hostels = $dbLink->getFileList($path."Hostel/");
foreach ($_hostels[0] as &$hostel) 
    echo "                    <option value='$hostel'>$hostel</option>\n";
?>
                </select>
            </div>
        </div>
        <!------------------Select file-------------------->
        <div class="form-group" id="div_path" filter="task-delete task-mkdir">
            <label class="control-label col-lg-2">File/Folder</label>
            <div class="col-lg-3" filter="task-delete">
                <select name="delete-data" class="form-control select-picker">
                    <option value="0" disabled selected >Select File</option>
<?php
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////CLEAN THIS CODE////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
foreach ($_files[0] as &$folder) {
        if ($folder=='Hostel') continue;
	foreach ($_files[$folder] as &$file) {
		if ($file == "." || $file == "..") continue;
		else echo "                    <option value='$folder/$file' filter='category-$folder'>$file</option>\n";
	}
}
foreach ($_hostels[0] as &$folder) {
	foreach ($_hostels[$folder] as &$file) {
		if ($file == "." || $file == "..") continue;
		else echo "                    <option value='Hostel/$folder/$file' filter='hostel-$folder'>$file</option>\n";
	}
}
?>
                </select>
            </div>
            <div class="col-lg-3" filter="task-mkdir">
                <input type="text" name="mkdir-data" value="" placeholder="" class="form-control">
            </div>
        </div>
        <div class="form-group" id="div_path" filter="task-upload" style="display: block;">
            <label class="control-label col-lg-2">Poster file</label>
            <div class="col-lg-6" filter="task-upload" style="display: block;">
                <input type="file" name="upload-poster" style="padding: 8px 1px">

            </div>
        </div>
        <div class="form-group" id="div_path" filter="task-upload" style="display: block;">
            <label class="control-label col-lg-2">Start date</label>
            <div class="col-lg-3" filter="task-upload" style="display: block;">

                <input type="date" name="upload-poster-start-date" style="padding: 8px 1px" min=<?php echo "'$today'"; ?> value=<?php 
echo "'$today'"; ?> required=true>

            </div>
            <div class="col-lg-3" filter="task-upload" style="display: block;">

 <label class="control-label" style="padding-right:10px">Expires after</label>
                <select name="upload-poster-expiry" style="padding: 8px 1px">
<?php
for ($i=1;$i<$maxExpiry["poster"];$i++) {
    $default=($i==$defaultExpiry["poster"])?"selected":"";
                echo "                    <option value='$i' $default>$i day(s)</option>";
}
?>
                </select>
            </div>
        </div>
        <div class="form-group" id="div_path" filter="task-upload" style="display: block;">
            <label class="control-label col-lg-2">Text file</label>
            <div class="col-lg-6" filter="task-upload" style="display: block;">
                <input type="file" name="upload-text" style="padding: 8px 1px">
            </div>
        </div>
         
        <div class="form-group" id="div_path" filter="task-upload" style="display: block;">
            <label class="control-label col-lg-2">Start date</label>
  
                <div class="col-lg-3" filter="task-upload" style="display: block;">
                    
                <input type="date" name="upload-text-start-date" style="padding: 8px 1px" min=<?php echo "'$today'"; ?> value=<?php echo 
"'$today'"; ?> required=true>

            </div>
                <div class="col-lg-3" filter="task-upload" style="display: block;">
 <label class="control-label" style="padding-right:10px">Expires after</label>
                <select name="upload-text-expiry" style="padding: 8px 1px">
<?php
for ($i=1;$i<$maxExpiry["text"];$i++) {
    $default=($i==$defaultExpiry["text"])?"selected":"";
                echo "                    <option value='$i' $default>$i day(s)</option>";
}
?>
		</select>
            </div>
        </div>
        <div class="form-group" id="div_name" filter="task-add-user" style="display: block;">
            <label class="control-label col-lg-2">Name</label>
            <div class="col-lg-3">
                <input name="user-name" type="text" class="form-control"/>
            </div>
        </div>    
        <div class="form-group" id="div_username" filter="task-add-user" style="display: block;">
            <label class="control-label col-lg-2">Username</label>
            <div class="col-lg-3">
                <input name="user-username" type="text" class="form-control"/>
            </div>
        </div>
        <div class="form-group" id="div_password" filter="task-add-user" style="display: block;">
          <label class="control-label col-lg-2">Password</label>
          <div class="col-lg-3">
            <input name="user-password" type="password" class="form-control"/>
          </div>
        </div>
        <div class="form-group" id="div_password2" filter="task-add-user" style="display: block;">
          <label class="control-label col-lg-2">Password again</label>
          <div class="col-lg-3">
            <input name="user-password-again" type="password" class="form-control"/>
          </div>
        </div>           

        <div class="form-group" id="div_files" filter="task-add-user" style="display: block;">
          <label class="control-label col-lg-2">Upload files</label>
          <div class="col-lg-3">
            <input name="user-permission-upload" type="checkbox" class="form-control"/>
          </div>
        </div>  
            
        <div class="form-group" id="div_deletefiles" filter="task-add-user" style="display: block;">
          <label class="control-label col-lg-2">Delete all files</label>
          <div class="col-lg-3">
            <input name="user-permission-delete" type="checkbox" class="form-control"/>
          </div>
        </div>

        <div class="form-group" id="div_permapp" filter="task-add-user" style="display: block;">
          <label class="control-label col-lg-2">Approve uploads</label>
          <div class="col-lg-3">
            <input name="user-permission-approve" type="checkbox" class="form-control"/>
          </div>
        </div>
            
        <div class="form-group" id="div_permusers" filter="task-add-user" style="display: block;">
          <label class="control-label col-lg-2">Add/Delete users</label>
          <div class="col-lg-3">
            <input name="user-permission-users" type="checkbox" class="form-control"/>
          </div>
        </div>  
        
        <div class="form-group" id="div_permpis" filter="task-add-user" style="display: block;">
          <label class="control-label col-lg-2">Add/Delete Pis</label>
          <div class="col-lg-3">
            <input name="user-permission-pis" type="checkbox" class="form-control"/>
          </div>
        </div>
<!--
        <div class="form-group" id="div_permpdir" filter="task-add-user" style="display: block;">
          <label class="control-label col-lg-2">Add/Delete Directories</label>
          <div class="col-lg-3">
            <input name="user-permission-dir" type="checkbox" class="form-control"/>
          </div>
        </div>  
-->
        <div class="form-group" filter="task-del-user">  
          <label class="control-label col-lg-2">Username</label>
          <div class="col-lg-3 ">
            <select name="deluser-id" class="form-control select-picker">
<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$users = $dbLink->getUsers();
foreach ($users as $auser) {
	echo '                <option value="'.$auser->Uid .'">'.$auser->Uid .'</option>';	
}
?> 

  </select>
              </div>
            </div>	 

            <div class="form-group" id="div_piip" filter="task-add-pi" style="display: block;">
              <label class="control-label col-lg-2">IP</label>
              <div class="col-lg-3">
              <div class="input-group"><input name="pi-ip" type="text" class="form-control" value="10."/>
              <span class="input-group-btn"><input type=text class="form-control" size=2 value=22 name="pi-port" 
style="min-width:60px;"></span>
              </div>
              </div>
            </div>
             <div class="form-group" id="div_piuser" filter="task-add-pi" style="display: block;">
              <label class="control-label col-lg-2">Username</label>
              <div class="col-lg-3">
              <input name="pi-uid" type="text" class="form-control"/>
              </div>
            </div>                 
             <div class="form-group" id="div_pipass" filter="task-add-pi" style="display: block;">
              <label class="control-label col-lg-2">Password</label>
              <div class="col-lg-3">
              <input name="pi-pass" type="password" class="form-control"/>
              </div>
            </div>
                            
       <div id="div_pihostel" class="form-group" filter="task-add-pi">  
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
            
             <div id="div_hostelpi" class="form-group" filter="task-del-pi">  
              <label class="control-label col-lg-2">Pi IP</label>
              <div class="col-lg-3 ">
              <select name="delpi-id" class="form-control select-picker" id="hostel" onChange="hostel_onChange()">
                <?
echo "\n";
$pis = $dbLink->getPis();
foreach ($pis as $pi) {
	echo '                <option value="'.$pi->PiID.'">'.$pi->IP.' (Hostel '.$pi->Hostel.')</option>';	
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
<!--            <span class="help-block">For complaints and suggestions click <a href="#">here</a><br></span>       -->
        </form> 
        <hr style="border:0.1px solid"> 
    </div> 
    <div class="well" id="page-history" style="top:100px; width:70%; position:absolute; left:20%; display:none;">
        <h2 style="text-align:center">History</h2>
        <hr style="border:1px solid">
        <table cellspacing="3" cellpadding="3">
            <thead>
                <tr>
                    <th>Date registered</th> <th>Path</th> <th>Directive</th> <th>Pi IP</th> <th>Hostel/Location</th> <th>Approved</th> 
                </tr>
            </thead>
            <tbody>
<?php
    $queue=$dbLink->getQueue();

    foreach($queue as $qitem){
        $appText="Approved";
        if(!$qitem->Approved){
            if($dbLink->hasPerm($user,APPROVE_UPLOAD)){
                $appText="<button type=button class='btn btn-default approvebtn' data-bpath='".$qitem->Path."'>Approve!</button>";
            }
            else {
                $appText="Pending";
            }
        }
        $Path=$qitem->Path;
        if ($qitem->Type!='upload') $appText="-NA-";
        echo "<tr class=approvaltr style='height: 40px;'><td class=approvaltd>".$qitem->Date ."</td><td class=approvaltd><a href='root/$Path'>$Path</a></td><td class=approvaltd>".$qitem->Type ."</td><td>".$qitem->IP ."</td><td>".$qitem->Hostel."</td><td class=approvaltd 
data-type='".$qitem->Type."' data-path='".$qitem->Path ."'>".$appText."</td></tr>";
    }
?>
            </tbody>
        </table>
        <script>
        function approveImg(path){
        console.log(path);
          $.post('action.php',{'task':'approve','path':path},function(data){
            if(data!="true"){return;};
            var approved=$('td[data-path="'+path+'"][data-type="upload"]');
            var index;
            for (index = 0; index < approved.length; ++index) 
                    approved[index].innerHTML="Approved";
            });
            return true;
        }
//        function showAll(){
//          $('.approvaltr,approvaltr td').show()
//        }
//        function hideSome(){
//          $('.approvaltr').each(function(){
//            path=$(this).data('path')
//            $('.approvaltr[data-path="'+path+'"][data-type=upload]').hide().first().show()
//            $('.approvaltr:not([data-type=Copy])').hide()
//        })
//        }
        $('.approvebtn').click(function(){approveImg($(this).attr('data-bpath'))})
        </script>
    </div>
  </div> 
</body>
</html>



