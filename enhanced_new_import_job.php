<?php
include "inc/module.inc.php";

function previewFiles() {
    var fileInput = document.getElementById('job_documents');
    var fileList = document.getElementById('file-list');
    var files = fileInput.files;
    
    if (files.length > 0) {
        fileList.style.display = 'block';
        var listHTML = '<h4 style="margin: 0 0 10px 0; color: #333;">Selected Files:</h4><ul style="margin: 0; padding-left: 20px;">';
        
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var fileSize = (file.size / 1024 / 1024).toFixed(2); // Convert to MB
            
            listHTML += '<li style="margin-bottom: 5px;">' + file.name + ' <span style="color: #666;">(' + fileSize + ' MB)</span></li>';
            
            // Validate file size
            if (file.size > 10 * 1024 * 1024) {
                alert('File "' + file.name + '" is too large. Maximum size is 10MB.');
                fileInput.value = '';
                fileList.style.display = 'none';
                return false;
            }
        }
        
        listHTML += '</ul>';
        fileList.innerHTML = listHTML;
    } else {
        fileList.style.display = 'none';
    }
}

// File upload handling
function handleFileUploads($job_no) {
    $uploadDir = "uploads/jobs/" . $job_no . "/";
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $uploadedFiles = array();
    
    // Handle multiple file uploads
    if (isset($_FILES['job_documents']) && !empty($_FILES['job_documents']['name'][0])) {
        $fileCount = count($_FILES['job_documents']['name']);
        
        for ($i = 0; $i < $fileCount; $i++) {
            if ($_FILES['job_documents']['error'][$i] == UPLOAD_ERR_OK) {
                $fileName = $_FILES['job_documents']['name'][$i];
                $fileSize = $_FILES['job_documents']['size'][$i];
                $fileTmp = $_FILES['job_documents']['tmp_name'][$i];
                $fileType = $_FILES['job_documents']['type'][$i];
                
                // Validate file size (max 10MB)
                if ($fileSize > 10 * 1024 * 1024) {
                    echo "<script>alert('File " . $fileName . " is too large. Maximum size is 10MB.');</script>";
                    continue;
                }
                
                // Validate file type
                $allowedTypes = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif', 'txt');
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                
                if (!in_array($fileExtension, $allowedTypes)) {
                    echo "<script>alert('File type not allowed for " . $fileName . ". Allowed types: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG, GIF, TXT');</script>";
                    continue;
                }
                
                // Generate unique filename to prevent conflicts
                $uniqueFileName = time() . "_" . $i . "_" . $fileName;
                $uploadPath = $uploadDir . $uniqueFileName;
                
                if (move_uploaded_file($fileTmp, $uploadPath)) {
                    $uploadedFiles[] = array(
                        'original_name' => $fileName,
                        'stored_name' => $uniqueFileName,
                        'file_path' => $uploadPath,
                        'file_size' => $fileSize,
                        'file_type' => $fileType
                    );
                }
            }
        }
    }
    
    return $uploadedFiles;
}

// Save file information to database
function saveFileInfo($job_no, $uploadedFiles) {
    global $db;
    
    foreach ($uploadedFiles as $file) {
        $fileData = array(
            'job_no' => $job_no,
            'original_filename' => $file['original_name'],
            'stored_filename' => $file['stored_name'],
            'file_path' => $file['file_path'],
            'file_size' => $file['file_size'],
            'file_type' => $file['file_type'],
            'upload_date' => date("Y-m-d H:i:s", time() + 28800),
            'uploaded_by' => $_SESSION['user_id']
        );
        
        $db->_insert_data('cms_job_files', $fileData);
    }
}


if($_REQUEST['add'])
{
	$add = $_REQUEST['add'];
	$job_date = date("Y-m-d",time()+28800);
	$job_shipper = strtoupper($_REQUEST['job_shipper']);
	$job_consignee = strtoupper($_REQUEST['job_consignee']);
	$job_awb = strtoupper($_REQUEST['job_awb']);
	$job_hawb = strtoupper($_REQUEST['job_hawb']);
	$job_vf = strtoupper($_REQUEST['job_vf']);
	$job_date_etd = strtoupper($_REQUEST['job_date_etd']);	
	$job_airport_arrival = strtoupper($_REQUEST['job_airport_arrival']);
	$job_airport_departure = strtoupper($_REQUEST['job_airport_departure']);
	$job_desc = strtoupper($_REQUEST['job_desc']);
	$job_date_collected = strtoupper($_REQUEST['job_date_collected']);
	$job_quantity = strtoupper($_REQUEST['job_quantity']);
	$job_date_eta = strtoupper($_REQUEST['job_date_eta']);	
	$job_term = strtoupper($_REQUEST['job_term']);
	$job_remark1 = strtoupper($_REQUEST['job_remark1']);
	
	$job_no = strtoupper($_REQUEST['job_no']);
	$job_vf2 = strtoupper($_REQUEST['job_vf2']);
	$job_quantity2 = strtoupper($_REQUEST['job_quantity2']);
	$job_date_arrival = strtoupper($_REQUEST['job_date_arrival']);
	$job_date_transfer = strtoupper($_REQUEST['job_date_transfer']);
	$job_storage = strtoupper($_REQUEST['job_storage']);
	$job_date_clearance = strtoupper($_REQUEST['job_date_clearance']);
	$job_duty = strtoupper($_REQUEST['job_duty']);
	$job_permit = strtoupper($_REQUEST['job_permit']);
	$job_date_delivery = strtoupper($_REQUEST['job_date_delivery']);
	$job_delivery_no = strtoupper($_REQUEST['job_delivery_no']);
	$job_vehicle = strtoupper($_REQUEST['job_vehicle']);
	$job_remark2 = strtoupper($_REQUEST['job_remark2']);

	$job_created_by = $_SESSION['user_id'];
	$job_type = 'IMPORT';
	$job_last_update = date("Y-m-d H:i:s",time()+28800);
}


if(isset($add))
{
	if($job_no == "" && $job_awb == "")
	{
		echo "<script>alert('Please check the JOB ID or MAWB no. Information are required!!');</script>";
	}
	else if ($job_no != "")
	{
		if ($job_awb != "")
		{
			$sqlcheck = "select count(*) as countryExist from cms_job where job_no='$job_no' AND job_awb = '$job_awb'";
			$result = mysql_query($sqlcheck);
			$row = mysql_fetch_object($result);
			
		}
		else 
		{
			$sqlcheck = "select count(*) as countryExist from cms_job where job_no='$job_no'";
			$result = mysql_query($sqlcheck);
			$row = mysql_fetch_object($result);
		}
			if($row->countryExist > 0)
			{
			echo "<script>alert('DUPLICATE ENTRY!! : The job is already exist.');document.location.href='history.back();';</script>\n";
			}
			else
			{
				$data = array(job_date=>$job_date,job_shipper=>$job_shipper,job_consignee=>$job_consignee,job_awb=>$job_awb,job_hawb=>$job_hawb,job_vf=>$job_vf,job_date_etd=>$job_date_etd,job_airport_arrival=>$job_airport_arrival,job_airport_departure=>$job_airport_departure,job_desc=>$job_desc,job_date_collected=>$job_date_collected,job_quantity=>$job_quantity,job_date_eta=>$job_date_eta,job_term=>$job_term,job_remark1=>$job_remark1,job_no=>$job_no,job_vf2=>$job_vf2,job_quantity2=>$job_quantity2,job_date_arrival=>$job_date_arrival,job_date_transfer=>$job_date_transfer,job_storage=>$job_storage,job_date_clearance=>$job_date_clearance,job_duty=>$job_duty,job_permit=>$job_permit,job_date_delivery=>$job_date_delivery,job_delivery_no=>$job_delivery_no,job_vehicle=>$job_vehicle,job_remark2=>$job_remark2,job_type=>$job_type,job_last_update=>$job_last_update,job_created_by=>$job_created_by);
			
				$db->_insert_data('cms_job',$data);
				
				// Handle file uploads after job is created
				$uploadedFiles = handleFileUploads($job_no);
				if (!empty($uploadedFiles)) {
					saveFileInfo($job_no, $uploadedFiles);
				}
				
				list($jobprevyear3, $jobprevmonth3, $jobno3) = split('[.]',$job_no);
				$data = array(seq_value=>$jobno3+1);
		
				$db->_edit_data('cms_seq1',$data,'seq_id','cargo');
				
				echo "<script>parent.location.href='main.php?module=job&job_no=$job_no&job_type=IMPORT';alert('New IMPORT job has been saved successfully.');</script>\n";
			}
	}
}
?>
<html>
<head>
<link rel="stylesheet" href="main.css" type="text/css">
<link rel="stylesheet" href="miText.css" type="text/css">
<link rel="stylesheet" type="text/css" media="all" href="skins/aqua/theme.css" title="Aqua" />

<!-- import the calendar script -->
<script type="text/javascript" src="calendar.js"></script>

<!-- import the language module -->
<script type="text/javascript" src="calendar-en.js"></script>


<!-- helper script that uses the calendar -->
<script type="text/javascript">

// File preview function
function previewFiles() {
    var fileInput = document.getElementById('job_documents');
    var fileList = document.getElementById('file-list');
    var files = fileInput.files;
    
    if (files.length > 0) {
        fileList.style.display = 'block';
        var listHTML = '<h4 style="margin: 0 0 10px 0; color: #333;">Selected Files:</h4><ul style="margin: 0; padding-left: 20px;">';
        
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var fileSize = (file.size / 1024 / 1024).toFixed(2); // Convert to MB
            
            listHTML += '<li style="margin-bottom: 5px;">' + file.name + ' <span style="color: #666;">(' + fileSize + ' MB)</span></li>';
            
            // Validate file size
            if (file.size > 10 * 1024 * 1024) {
                alert('File "' + file.name + '" is too large. Maximum size is 10MB.');
                fileInput.value = '';
                fileList.style.display = 'none';
                return false;
            }
        }
        
        listHTML += '</ul>';
        fileList.innerHTML = listHTML;
    } else {
        fileList.style.display = 'none';
    }
}

// This function gets called when the end-user clicks on some date.
function selected(cal, date) {
  cal.sel.value = date; // just update the date in the input field.
  if (cal.dateClicked && (cal.sel.id == "job_date" || cal.sel.id == "job_date_required" || cal.sel.id == "job_date_collected" || cal.sel.id == "job_date_arrival" || cal.sel.id == "job_date_transfer" || cal.sel.id == "job_date_clearance" || cal.sel.id == "job_date_delivery" || cal.sel.id == "job_date_eta" || cal.sel.id == "job_date_eta"))
    // if we add this call we close the calendar on single-click.
    // just to exemplify both cases, we are using this only for the 1st
    // and the 3rd field, while 2nd and 4th will still require double-click.
    cal.callCloseHandler();
}

// And this gets called when the end-user clicks on the _selected_ date,
// or clicks on the "Close" button.  It just hides the calendar without
// destroying it.
function closeHandler(cal) {
  cal.hide();                        // hide the calendar
//  cal.destroy();
  _dynarch_popupCalendar = null;
}

// This function shows the calendar under the element having the given id.
// It takes care of catching "mousedown" signals on document and hiding the
// calendar if the click was outside.
function showCalendar(id, format, showsTime, showsOtherMonths) {
  var el = document.getElementById(id);
  if (_dynarch_popupCalendar != null) {
    // we already have some calendar created
    _dynarch_popupCalendar.hide();                 // so we hide it first.
  } else {
    // first-time call, create the calendar.
    var cal = new Calendar(1, null, selected, closeHandler);
    // uncomment the following line to hide the week numbers
    // cal.weekNumbers = false;
    if (typeof showsTime == "string") {
      cal.showsTime = true;
      cal.time24 = (showsTime == "24");
    }
    if (showsOtherMonths) {
      cal.showsOtherMonths = true;
    }
    _dynarch_popupCalendar = cal;                  // remember it in the global var
    cal.setRange(1900, 2070);        // min/max year allowed.
    cal.create();
  }
  _dynarch_popupCalendar.setDateFormat(format);    // set the specified date format
  _dynarch_popupCalendar.parseDate(el.value);      // try to parse the text in field
  _dynarch_popupCalendar.sel = el;                 // inform it what input field we use

  // the reference element that we pass to showAtElement is the button that
  // triggers the calendar.  In this example we align the calendar bottom-right
  // to the button.
  _dynarch_popupCalendar.showAtElement(el.nextSibling, "Br");        // show the calendar

  return false;
}

var MINUTE = 60 * 1000;
var HOUR = 60 * MINUTE;
var DAY = 24 * HOUR;
var WEEK = 7 * DAY;

// If this handler returns true then the "date" given as
// parameter will be disabled.  In this example we enable
// only days within a range of 10 days from the current
// date.
// You can use the functions date.getFullYear() -- returns the year
// as 4 digit number, date.getMonth() -- returns the month as 0..11,
// and date.getDate() -- returns the date of the month as 1..31, to
// make heavy calculations here.  However, beware that this function
// should be very fast, as it is called for each day in a month when
// the calendar is (re)constructed.
function isDisabled(date) {
  var today = new Date();
  return (Math.abs(date.getTime() - today.getTime()) / DAY) > 10;
}

function checkForm(){ 
  if (document.getElementById('job_no').value == "" || document.getElementById('job_awb').value == "")
  	{
  		alert("Please check the JOB ID or AWB no. Information are required!!"); 
  		event.returnValue=false;
		return false;
	}
}
</script>
<style type="text/css">
/*- Menu Tabs F--------------------------- */

    #tabsF {
      float:none;
      width:450px;
      background:url("images/bg.gif") repeat;
      font-size:93%;
      line-height:normal;
	  margin-left:5px;
	  border-bottom:solid 1px #666666;
      }
    #tabsF ul {
        margin:0;
        padding:10px 10px 0 50px;
        list-style:none;
      }
    #tabsF li {
      display:inline;
      margin:0;
      padding:0;
      }
    #tabsF a {
      float:left;
      background:url("images/tableftF.gif") no-repeat left top;
      margin:0;
      padding:0 0 0 4px;
      text-decoration:none;
      }
    #tabsF a span {
      float:left;
      display:block;
      background:url("images/tabrightF.gif") no-repeat right top;
      padding:5px 15px 4px 6px;
      color:#333333;
      }
    /* Commented Backslash Hack hides rule from IE5-Mac \*/
    #tabsF a span {float:none;}
    /* End IE5-Mac hack */
    #tabsF a:hover span {
      color:#FFFFFF;
      }
    #tabsF a:hover {
      background-position:0% -42px;
      }
    #tabsF a:hover span {
      background-position:100% -42px;
      }

        #tabsF #current a {
                background-position:0% -42px;
				cursor:auto;
        }
        #tabsF #current a span {
                background-position:100% -42px;
				cursor:auto;
        }

/* File upload styles */
.file-upload-section {
    background: #f9f9f9;
    border: 1px solid #ddd;
    padding: 15px;
    margin: 10px 0;
    border-radius: 5px;
}

.file-upload-section h3 {
    margin: 0 0 10px 0;
    color: #333;
    font-size: 14px;
}

.file-input-wrapper {
    position: relative;
    display: inline-block;
    cursor: pointer;
    background: #4CAF50;
    color: white;
    padding: 8px 16px;
    border-radius: 4px;
    font-size: 12px;
}

.file-input-wrapper input[type=file] {
    position: absolute;
    left: -9999px;
}

.file-input-wrapper:hover {
    background: #45a049;
}

#file-list {
    margin-top: 10px;
    padding: 10px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 3px;
    display: none;
}

#file-list ul {
    margin: 0;
    padding-left: 20px;
}

#file-list li {
    margin-bottom: 5px;
    font-size: 12px;
}
</style>
</head>
<body bgcolor="#FFFFFF" text="#000000" style="background:url(images/bg.gif) repeat">
<form method="post" name="newjob" onSubmit="return checkForm();" action="main.php?module=new_import_job" enctype="multipart/form-data">
<table width="100%" border="0" cellspacing="0" cellpadding="0" >
        
        <tr>
          <td valign=top width="2%"></td>
          <td width="98%" valign=top><table width="100%" border="0" cellspacing="0" cellpadding="0">
            

            <tr valign=top>
              <td colspan="4" class="spanmiTextCaption"><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr valign=top>
              <td width="5111808" colspan="4" class="spanmiTextCaptionFeat"><div id="tabsF">
                                <ul>
                                        <!-- CSS Tabs -->
<li><a href="main.php?module=job"><span>Jobs</span></a></li>
<li id="current"><a><span>New Jobs</span></a></li>
<li><a href="main.php?module=incoming_job"><span>Import Jobs</span></a></li>
<li><a href="main.php?module=outgoing_job"><span>Export Jobs</span></a></li>
                                </ul>
                        </div>			  </td>
            </tr>
            <tr valign=top>
              <td colspan="4" class="spanmiTextCaptionPromo" align="char">&nbsp;</td>
            </tr>
            <tr valign=top>
              <td colspan="4" class="spanmiTextCaptionPromo" align="char">&nbsp;<span class="spanmiTextCaptionFeat"><span class="spanmiTextCaptionPromo style2 style1 style1"><img src="images/newjob.png" width="16" height="16" align="absbottom"></span></span>&nbsp;New Job Details :<span class="spanmiTextCaptionFeat"><span class="spanmiTextCaptionPromo style1"> </span><span class="spanmiTextCaptionPromo style2 style1 style1"><span class="spanmiTextCaptionPromo style1"><img src="images/gray_arrow.gif" alt="details" width="7" height="5"></span></span></span>
                <input name="radiobutton" type="radio" value="radiobutton" checked="checked">
                IMPORT&nbsp;
			    <input name="radiobutton" type="radio" value="radiobutton" onClick="window.location='main.php?module=new_export_job'">
			    EXPORT			  </td>
            </tr>
            
            </table> </td>
            </tr>
            <tr valign=top>
              <td colspan="4" class="spanmiTextCaption">&nbsp;</td>
            </tr>
            <tr valign=top>
              <td colspan="4" class="spanmiTextCaption">IMPORT JOB </td>
            </tr>
            <tr valign=top>
              <td colspan="4" class="spanmiTextCaptionPromo">--------------------------------------( AT ORIGIN )---------------------------------------</td>
            </tr>
            <tr valign=top>
              <td colspan="4">&nbsp;</td>
            </tr>
            
            <tr valign=top>
              <td colspan="2" class="spanmiTextCaptionPromo">Shipper : </td>
              <td colspan="2"><textarea name="job_shipper" cols="50" rows="5" class="spanmiTextCaptionPromo11" id="job_shipper"></textarea></td>
            </tr>
            
			<tr valign=top>
              <td colspan="2" class="spanmiTextCaptionPromo">Consignee :</td>
              <td colspan="2"><textarea name="job_consignee" cols="50" rows="5" class="spanmiTextCaptionPromo11" id="job_consignee"></textarea></td>
            </tr>
            
            
			<tr valign=top>
              <td width="26%" class="spanmiTextCaptionPromo">MAWB  No : </td>
              <td width="13%" align="right" class="readOnly">*required</td>
              <td colspan="2"><input name="job_awb" type="text" class="spanmiTextCaptionPromo11" id="job_awb" size="50" maxlength="200" /></td>
			</tr>
            <tr valign=top>
              <td colspan="2" class="spanmiTextCaptionPromo">HAWB BL No : </td>
              <td colspan="2"><input name="job_hawb" type="text" class="spanmiTextCaptionPromo11" id="job_hawb" size="50" maxlength="200" /></td>
            </tr>
			
            <tr valign=top>
              <td colspan="2" align="left" class="spanmiTextCaptionPromo">Flight No : </td>
              <td colspan="2"><input name="job_vf" type="text" class="spanmiTextCaptionPromo11" id="job_vf" size="30" maxlength="200" /></td>
            </tr>
            <tr valign=top>
              <td colspan="2" align="left" class="spanmiTextCaptionPromo">ETD Date : </td>
              <td colspan="2"><input readonly="true" name="job_date_etd" type="text" class="spanmiTextCaptionPromo" id="job_date_etd" size="20" maxlength="50" onClick="return showCalendar('job_date_etd', '%Y-%m-%d', '24', true);" /><img style="margin-left:3px" src="images/calendar1.gif" alt="Show Calendar" width="16" height="15" onClick="return showCalendar('job_date_etd', '%Y-%m-%d', '24', true);" /></td>
            </tr>
			<tr valign=top>
              <td colspan="2" class="spanmiTextCaptionPromo">Departure Airport : </td>
              <td colspan="2"><textarea name="job_airport_departure" cols="50" rows="3" class="spanmiTextCaptionPromo11" id="job_airport_departure"></textarea></td>
            </tr>
			<tr valign=top>
              <td colspan="2" class="spanmiTextCaptionPromo">Arrival Airport : </td>
              <td colspan="2"><textarea name="job_airport_arrival" cols="50" rows="3" class="spanmiTextCaptionPromo11" id="job_airport_arrival"></textarea></td>
            </tr>
            <tr valign="top">
              <td colspan="2" class="spanmiTextCaptionPromo">Commodity : </td>
              <td colspan="2"><textarea name="job_desc" cols="50" rows="5" class="spanmiTextCaptionPromo11" id="job_desc"></textarea></td>
            </tr>
			<tr valign=top>
              <td colspan="2" class="spanmiTextCaptionPromo">Date Cargo Collected / Received : </td>
              <td colspan="2"><input readonly="true" name="job_date_collected" type="text" class="spanmiTextCaptionPromo" id="job_date_collected" size="20" maxlength="50" onClick="return showCalendar('job_date_collected', '%Y-%m-%d', '24', true);" /><img style="margin-left:3px" src="images/calendar1.gif" alt="Show Calendar" width="16" height="15" onClick="return showCalendar('job_date_collected', '%Y-%m-%d', '24', true);" /></td>
            </tr>
            <tr valign=top>
              <td colspan="2" class="spanmiTextCaptionPromo">No of pieces / Weight / Carton : </td>
              <td colspan="2"><input name="job_quantity" type="text" class="spanmiTextCaptionPromo11" id="job_quantity" size="50" maxlength="200" /></td>
            </tr>
			<tr valign=top>
              <td colspan="2" align="left" class="spanmiTextCaptionPromo">Arrival Date : </td>
              <td colspan="2"><input readonly="true" name="job_date_eta" type="text" class="spanmiTextCaptionPromo" id="job_date_eta" size="20" maxlength="50" onClick="return showCalendar('job_date_eta', '%Y-%m-%d', '24', true);" /><img style="margin-left:3px" src="images/calendar1.gif" alt="Show Calendar" width="16" height="15" onClick="return showCalendar('job_date_eta', '%Y-%m-%d', '24', true);" /></td>
            </tr>
            <tr valign=top>
              <td colspan="2" class="spanmiTextCaptionPromo">Term :</td>
              <td colspan="2">
			  <select name="job_term" class="spanmiTextCaptionPromo" id="job_term">
                <option value="EXW">EXW</option>
                <option value="FOB">FOB</option>
                <option value="CIF" selected>CIF</option>
                <option value="DDP">DDP</option>
                <option value="DDU">DDU</option>
              </select></td>
            </tr>
            
            <tr valign=top>
              <td colspan="2" class="spanmiTextCaptionPromo">Remark : </td>
              <td colspan="2"><textarea name="job_remark1" cols="50" rows="5" class="spanmiTextCaptionPromo11" id="job_remark1"></textarea></td>
            </tr>
            <tr valign=top>
              <td colspan="2" class="spanmiTextCaptionPromo">&nbsp;</td>
              <td colspan="2">&nbsp;</td>
            </tr>
          <tr valign=top>
              <td colspan="4" class="spanmiTextCaptionPromo">--------------------------------------( AT DESTINATION )---------------------------------------</td>
            </tr>
				
            <tr valign=top>
              <td colspan="2" class="spanmiTextCaptionPromo">&nbsp;</td>
              <td colspan="2">&nbsp;</td>
            </tr>
			
			 <tr valign=top>
              <td colspan="2" class="spanmiTextCaptionPromo">SI / Job No : </td>
              <td width="26%"><input name="job_no" type="text" class="spanmiTextCaptionPromoUnique" id="job_no" value="<?='IM-'.$seq->get_job_no(1)?>" size="20" maxlength="50" readonly="true" /></td>
              <td width="35%" class="readOnly">*sequence job no </td>
            </tr>
             <tr valign=top>
               <td colspan="2" class="spanmiTextCaptionPromo">Flight No : </td>
               <td colspan="2"><input name="job_vf2" type="text" class="spanmiTextCaptionPromo11" id="job_vf2" size="30" maxlength="200" /></td>
             </tr>
            <tr valign=top>
              <td colspan="2" class="spanmiTextCaptionPromo">No of pieces / Weight / Carton : </td>
              <td colspan="2"><input name="job_quantity2" type="text" class="spanmiTextCaptionPromo11" id="job_quantity2" size="50" maxlength="200" /></td>
            </tr>
            <tr valign=top>
              <td colspan="2" class="spanmiTextCaptionPromo">Date of Arrival : </td>
              <td colspan="2"><input readonly="true" name="job_date_arrival" type="text" class="spanmiTextCaptionPromo" id="job_date_arrival" size="20" maxlength="50" onClick="return showCalendar('job_date_arrival', '%Y-%m-%d', '24', true);" /><img style="margin-left:3px" src="images/calendar1.gif" alt="Show Calendar" width="16" height="15" onClick="return showCalendar('job_date_arrival', '%Y-%m-%d', '24', true);" /></td>
            </tr>
            
            <tr valign=top>
              <td colspan="2" class="spanmiTextCaptionPromo">Date Cargo Transfer : </td>
              <td colspan="2"><input readonly="true" name="job_date_transfer" type="text" class="spanmiTextCaptionPromo" id="job_date_transfer" size="20" maxlength="50" onClick="return showCalendar('job_date_transfer', '%Y-%m-%d', '24', true);" /><img style="margin-left:3px" src="images/calendar1.gif" alt="Show Calendar" width="16" height="15" onClick="return showCalendar('job_date_transfer', '%Y-%m-%d', '24', true);" /></td>
            </tr>
            <tr valign=top>
              <td colspan="2" class="spanmiTextCaptionPromo">Storage Due At MF WHSE : </td>
              <td colspan="2"><input name="job_storage" type="text" class="spanmiTextCaptionPromo11" id="job_storage" size="30" maxlength="200" /></td>
            </tr>
            <tr valign=top>
              <td colspan="2" class="spanmiTextCaptionPromo">Custom Clearance Date : </td>
              <td colspan="2"><input readonly="true" name="job_date_clearance" type="text" class="spanmiTextCaptionPromo" id="job_date_clearance" size="20" maxlength="50" onClick="return showCalendar('job_date_clearance', '%Y-%m-%d', '24', true);" /><img style="margin-left:3px" src="images/calendar1.gif" alt="Show Calendar" width="16" height="15" onClick="return showCalendar('job_date_clearance', '%Y-%m-%d', '24', true);" /></td>
            </tr>
            <tr valign=top>
              <td colspan="2" class="spanmiTextCaptionPromo">Dutiable / Non Dutiable : </td>
              <td colspan="2"><input name="job_duty" type="text" class="spanmiTextCaptionPromo11" id="job_duty" size="50" maxlength="200" /></td>
            </tr>
                 <tr valign=top>
              <td colspan="2" class="spanmiTextCaptionPromo">Import Permit  :</td>
              <td colspan="2">
			  <select name="job_permit" class="spanmiTextCaptionPromo" id="job_permit">
			    <option value="YES">YES</option>
			    <option value="NO">NO</option>
              </select></td>
            </tr>
            <tr valign=top>
              <td colspan="2" class="spanmiTextCaptionPromo">Date of delivery : </td>
              <td colspan="2"><input readonly="true" name="job_date_delivery" type="text" class="spanmiTextCaptionPromo" id="job_date_delivery" size="20" maxlength="50" onClick="return showCalendar('job_date_delivery', '%Y-%m-%d', '24', true);" /><img style="margin-left:3px" src="images/calendar1.gif" alt="Show Calendar" width="16" height="15" onClick="return showCalendar('job_date_delivery', '%Y-%m-%d', '24', true);" /></td>
            </tr>
            
            
            
            <tr valign=top>
              <td colspan="2" class="spanmiTextCaptionPromo">Delivery Order No : </td>
              <td colspan="2"><input name="job_delivery_no" type="text" class="spanmiTextCaptionPromo11" id="job_delivery_no" size="50" maxlength="200" /></td>
            </tr>
            <tr valign=top>
              <td colspan="2" class="spanmiTextCaptionPromo">Truck No : </td>
              <td colspan="2"><input name="job_vehicle" type="text" class="spanmiTextCaptionPromo11" id="job_vehicle" size="50" maxlength="200" /></td>
            </tr>
            <tr valign=top>
              <td colspan="2" class="spanmiTextCaptionPromo">Remark : </td>
              <td colspan="2"><textarea name="job_remark2" cols="50" rows="5" class="spanmiTextCaptionPromo11" id="job_remark2"></textarea></td>
            </tr>

            <!-- File Upload Section -->
            <tr valign=top>
              <td colspan="4" class="spanmiTextCaptionPromo">--------------------------------------( DOCUMENT UPLOADS )---------------------------------------</td>
            </tr>
            <tr valign=top>
              <td colspan="4">&nbsp;</td>
            </tr>
            <tr valign=top>
              <td colspan="4">
                <div class="file-upload-section">
                  <h3>📎 Upload Job Documents</h3>
                  <p style="font-size: 11px; color: #666; margin: 5px 0;">
                    Upload relevant documents such as invoices, packing lists, certificates, etc.<br>
                    <strong>Allowed formats:</strong> PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG, GIF, TXT<br>
                    <strong>Maximum size:</strong> 10MB per file
                  </p>
                  
                  <div class="file-input-wrapper">
                    <input type="file" name="job_documents[]" id="job_documents" multiple 
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.txt"
                           onchange="previewFiles()">
                    📁 Choose Files
                  </div>
                  
                  <div id="file-list" style="display: none;"></div>
                </div>
              </td>
            </tr>
			
          </table>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="center"><br>
                    <br>
                    <input name="job_created_by" value="<?=$_GET['userid']?>" type="hidden" id="job_created_by" />
					<input name="add" type="hidden" id="add" value="add">
                    <input onClick="return checkForm();" type="image" src="images/save.gif" width="62" height="20" border="0">
                    <a onClick="history.back();"><img src="images/cancel.gif" width="58" height="20" border="0"></a> </td>
              </tr>
          </table></td>
        </tr>
  </table>


</form>
</body>
</html>