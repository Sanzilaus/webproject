<?php
   function ValidateEmail($email)
   {
      $pattern = '/^([0-9a-z]([-.\w]*[0-9a-z])*@(([0-9a-z])+([-\w]*[0-9a-z])*\.)+[a-z]{2,6})$/i';
      return preg_match($pattern, $email);
   }

   function RecursiveMkdir($path)
   {
      if (!file_exists($path))
      {
         RecursiveMkdir(dirname($path));
         mkdir($path, 0777);
      }
   }

   if($_SERVER['REQUEST_METHOD'] == 'POST')
   {
      $mailto = 'sunzeelus@yahoo.com';
      $mailfrom = isset($_POST['email']) ? $_POST['email'] : $mailto;
      $subject = 'Website form';
      $message = 'Values submitted from web site form:';
      $success_url = '';
      $error_url = '';
      $error = '';
      $eol = "\n";
      $max_filesize = isset($_POST['filesize']) ? $_POST['filesize'] * 1024 : 1024000;
      $upload_folder = "upload";
      $upload_folder = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME'])."/".$upload_folder;

      $boundary = md5(uniqid(time()));

      $header  = 'From: '.$mailfrom.$eol;
      $header .= 'Reply-To: '.$mailfrom.$eol;
      $header .= 'MIME-Version: 1.0'.$eol;
      $header .= 'Content-Type: multipart/mixed; boundary="'.$boundary.'"'.$eol;
      $header .= 'X-Mailer: PHP v'.phpversion().$eol;
      if (!ValidateEmail($mailfrom))
      {
         $error .= "The specified email address is invalid!\n<br>";
      }

      $prefix = rand(111111, 999999);

      $i = 0;
      while (list ($key, $val) = each ($_FILES))
      {
         if ($_FILES[$key]['name'] != "" and file_exists($_FILES[$key]['tmp_name']) and $_FILES[$key]['size'] > 0)
         {
            $upload_DstName[$i] = $prefix . "_" . str_replace(" ", "_", $_FILES[$key]['name']);
            $upload_SrcName[$i] = $_FILES[$key]['name'];
            $upload_Size[$i] = ($_FILES[$key]['size']);
            $upload_Temp[$i] = ($_FILES[$key]['tmp_name']);
            $upload_Type[$i] = ($_FILES[$key]['type']);
            $uploadlink[$i] = "$upload_folder/$upload_DstName[$i]";
            $upload_fieldname[$i] = $key;
            $upload_fieldname_final[$i] = ucwords(str_replace("_", " ", $key));
            $fieldvalue[$i] = $uploadlink[$i];
            $i++;
         }
         if ($upload_Size[$i] >= $max_filesize)
         {
            $error .= "The size of $key (file: $upload_SrcName[$i]) is bigger than the allowed " . $max_filesize/1024 . " Kbytes!\n";
         }
      }

      if (!empty($error))
      {
         $errorcode = file_get_contents($error_url);
         $replace = "##error##";
         $errorcode = str_replace($replace, $error, $errorcode);
         echo $errorcode;
         exit;
      }

      $uploadfolder = basename($upload_folder);
      for ($i = 0; $i < count($upload_DstName); $i++)
      {
         $uploadFile = $uploadfolder . "/" . $upload_DstName[$i];
         if (!is_dir(dirname($uploadFile)))
         {
            RecursiveMkdir(dirname($uploadFile));
         }
         else
         {
            chmod(dirname($uploadFile), 0777);
         }
         move_uploaded_file($upload_Temp[$i] , $uploadFile);
         chmod($uploadFile, 0644);
      }

      $internalfields = array ("submit", "reset", "send", "captcha_code");
      $message .= $eol;
      $message .= "IP Address : ";
      $message .= $_SERVER['REMOTE_ADDR'];
      $message .= $eol;
      foreach ($_POST as $key => $value)
      {
         if (!in_array(strtolower($key), $internalfields))
         {
            if (!is_array($value))
            {
               $message .= ucwords(str_replace("_", " ", $key)) . " : " . $value . $eol;
            }
            else
            {
               $message .= ucwords(str_replace("_", " ", $key)) . " : " . implode(",", $value) . $eol;
            }
         }
      }

      if (count($upload_SrcName) > 0)
      {
         $message .= "\nThe following files have been uploaded:\n";
         for ($i = 0; $i < count($upload_SrcName); $i++)
         {
            $message .= $upload_SrcName[$i] . " Link: " . $uploadlink[$i] . "\n";
         }
      }
      $body  = 'This is a multi-part message in MIME format.'.$eol.$eol;
      $body .= '--'.$boundary.$eol;
      $body .= 'Content-Type: text/plain; charset=ISO-8859-1'.$eol;
      $body .= 'Content-Transfer-Encoding: 8bit'.$eol;
      $body .= $eol.stripslashes($message).$eol;
      $body .= '--'.$boundary.'--'.$eol;
      mail($mailto, $subject, $body, $header);
      header('Location: '.$success_url);
      exit;
   }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>Contacts</title>
<meta name="generator" content="WYSIWYG Web Builder - http://www.wysiwygwebbuilder.com">
<style type="text/css">
body
{
   background-color: #996633;
   color: #000000;
   scrollbar-face-color: #ECE9D8;
   scrollbar-arrow-color: #000000;
   scrollbar-3dlight-color: #ECE9D8;
   scrollbar-darkshadow-color: #716F64;
   scrollbar-highlight-color: #FFFFFF;
   scrollbar-shadow-color: #ACA899;
   scrollbar-track-color: #D4D0C8;
}
</style>
<link rel="stylesheet" href="./wb.validation.css" type="text/css">
<script type="text/javascript" src="./jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="./wb.validation.js"></script>
<script type="text/javascript">
$(document).ready(function()
{
   $("#TextArea1").validate(
   {
      required: false,
      type: 'text',
      error_text: 'Type your Text here'
   });
   $("#TextArea2").validate(
   {
      required: false,
      type: 'email',
      error_text: 'Next attempt '
   });
   $("#TextArea3").validate(
   {
      required: false,
      type: 'number',
      expr_min: '',
      expr_max: '',
      value_min: '',
      value_max: '',
      error_text: 'Phone Number Please'
   });
   $("#TextArea4").validate(
   {
      required: true,
      type: 'custom',
      param: /^[A-Za-zƒŠŒšœŸÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏĞÑÒÓÔÕÖØÙÚÛÜİŞßàáâãäåæçèéêëìíîïğñòóôõöøùúûüışÿ]*$/,
      error_text: 'your name please'
   });
});
</script>
</head>
<body>
<div id="wb_" style="position:absolute;background-color:#F7F9FC;left:14px;top:321px;width:555px;height:380px;z-index:0">
<form name="Form1" method="post" action="<?php echo basename(__FILE__); ?>" enctype="multipart/form-data" id="">
</form>
</div>
<div id="wb_Shape2" style="margin:0;padding:0;position:absolute;left:11px;top:275px;width:558px;height:45px;text-align:left;z-index:1;">
<img src="images/img0023.gif" id="Shape2" alt="" title="" style="border-width:0;width:558px;height:45px"></div>
<div id="wb_Shape1" style="margin:0;padding:0;position:absolute;left:11px;top:0px;width:558px;height:34px;text-align:left;z-index:2;">
<img src="images/img0024.gif" id="Shape1" alt="" title="" style="border-width:0;width:558px;height:34px"></div>
<div id="wb_Text1" style="margin:0;padding:0;position:absolute;left:253px;top:6px;width:115px;height:18px;text-align:left;z-index:3;">
<font style="font-size:15px" color="#192666" face="Tahoma"><b>CONTACTS</b></font></div>
<div id="wb_Text3" style="margin:0;padding:0;position:absolute;left:160px;top:52px;width:409px;height:192px;text-align:left;z-index:4;">
<font style="font-size:13px" color="#192666" face="Tahoma"><b>LOCATION<br>
</b><br>
</font><font style="font-size:13px" color="#000000" face="Tahoma">&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; KISII&nbsp;&nbsp; -&nbsp; NAKURU&nbsp; -&nbsp; KENYA</font><font style="font-size:13px" color="#192666" face="Tahoma"><br>
<br>
<b>PHYSICAL ADDRESS<br>
</b><br>
</font><font style="font-size:13px" color="#000000" face="Tahoma">&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; SANZILAUS N. ONSERIO<br>
&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; P.O BOX 14,<br>
&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; NAKURU<br>
</font><font style="font-size:13px" color="#192666" face="Tahoma"><br>
<b>PHONE:</b>&nbsp;&nbsp;&nbsp;&nbsp; </font><font style="font-size:13px" color="#000000" face="Tahoma">0716232091</font><font style="font-size:13px" color="#192666" face="Tahoma"><br>
<b>EMAIL:</b>&nbsp;&nbsp; &nbsp;&nbsp; </font><font style="font-size:13px" color="#000000" face="Tahoma">sunzeelus@yahoo.com </font></div>
<div id="wb_Text4" style="margin:0;padding:0;position:absolute;left:254px;top:283px;width:115px;height:18px;text-align:left;z-index:5;">
<font style="font-size:15px" color="#192666" face="Tahoma"><b>FEEDBACK</b></font></div>
<div id="wb_Text5" style="margin:0;padding:0;position:absolute;left:133px;top:351px;width:97px;height:16px;text-align:left;z-index:6;">
<font style="font-size:13px" color="#639CCE" face="Tahoma"><b>NAME</b></font><font style="font-size:13px" color="#639CCE" face="Georgia"><b>:</b></font></div>
<div id="wb_Text6" style="margin:0;padding:0;position:absolute;left:134px;top:402px;width:97px;height:16px;text-align:left;z-index:7;">
<font style="font-size:13px" color="#639CCE" face="Tahoma"><b>PHONE NO.</b></font></div>
<div id="wb_Text7" style="margin:0;padding:0;position:absolute;left:133px;top:454px;width:97px;height:16px;text-align:left;z-index:8;">
<font style="font-size:13px" color="#639CCE" face="Tahoma"><b>EMAIL:</b></font></div>
<div id="wb_Text8" style="margin:0;padding:0;position:absolute;left:134px;top:510px;width:97px;height:16px;text-align:left;z-index:9;">
<font style="font-size:13px" color="#639CCE" face="Tahoma"><b>COMMENT</b></font><font style="font-size:13px" color="#FFD700" face="Tahoma"><b>:</b></font></div>
<textarea name="TextArea1" id="TextArea1" style="position:absolute;left:228px;top:489px;width:187px;height:92px;border:1px #C0C0C0 solid;font-family:Georgia;font-size:13px;z-index:10" rows="4" cols="25"></textarea>
<input type="submit" id="Button2" name="" value="SEND" style="position:absolute;left:246px;top:608px;width:96px;height:25px;background-color:#FFFFFF;color:#639CCE;font-family:Georgia;font-weight:bold;font-size:15px;z-index:11">
<textarea name="TextArea1" id="TextArea2" style="position:absolute;left:260px;top:446px;width:156px;height:18px;border:1px #C0C0C0 solid;font-family:Georgia;font-weight:bold;font-size:13px;z-index:12" rows="0" cols="17"></textarea>
<textarea name="TextArea1" id="TextArea3" style="position:absolute;left:261px;top:395px;width:155px;height:18px;border:1px #C0C0C0 solid;font-family:Georgia;font-weight:bold;font-size:13px;z-index:13" rows="0" cols="17"></textarea>
<textarea name="TextArea1" id="TextArea4" style="position:absolute;left:260px;top:344px;width:155px;height:18px;border:1px #C0C0C0 solid;font-family:Georgia;font-weight:bold;font-size:13px;z-index:14" rows="0" cols="17"></textarea>
</body>
</html>