<?php

$path = $_SERVER['DOCUMENT_ROOT'] ;
define( 'SHORTINIT', true );
require( $path.'/wp-load.php' );

global $wpdb;

if(isset($_REQUEST['lastdatepicker']) and !empty($_REQUEST['lastdatepicker']))
{

  $uploaddir = $path;
  $uploadfile1 = $uploaddir . basename($_FILES['file1']['name']);
  $uploadfile2 = $uploaddir . basename($_FILES['file2']['name']);

  if (move_uploaded_file($_FILES['file1']['tmp_name'], $uploadfile1)) {
      echo "File 1 is valid, and was successfully uploaded.\n";
  } else {  echo "Possible file upload attack!\n";  }

  if (move_uploaded_file($_FILES['file2']['tmp_name'], $uploadfile2)) {
      echo "File 2 is valid, and was successfully uploaded.\n";
  } else {   echo "Possible file upload attack!\n";
  }

  $imageprefix = $_REQUEST['prefix'] ;

  $start = (new DateTime($_REQUEST['firstdatepicker']))->modify('first day of this month');
  $end = (new DateTime($_REQUEST['lastdatepicker']))->modify('first day of next month');
  $interval = DateInterval::createFromDateString('1 month');
  $period   = new DatePeriod($start, $interval, $end);
  $metavalue = '';

  foreach ($period as $dt) {
      $date = $dt->format("Y-m");
      $m_value = str_replace("-","/",$date);
      $metavalue .= 'OR  pm.meta_value Like "%'.$m_value.'%"' ;
  }
  $metavalue = ltrim($metavalue,"OR");

$postarray =  getpostdetails($metavalue);
$inamearray =  getsubpostname($postarray,$metavalue);
changeimagename($inamearray,$uploadfile1,$uploadfile2,$path);
}


function changeimagename($inamearray,$uploadfile1,$uploadfile2,$path)
{
  //print_r($inamearray);
  global $wpdb;
  foreach($inamearray as $key => $value2)
  {
    $posttitle = $key;
    foreach($value2 as $key =>$value) {
    $cimagename = $value['meta_value'];
    $imagetitle = $value['post_title'];
    $mediaID = $value['ID'];
    $meta_id = $value['meta_id'];

    $info = pathinfo($cimagename);

    $dir = $info['dirname'];
    $originalname = $info['filename'];
    $format = $info['extension'];

    echo $origniname = origniname($originalname); echo '-';
    echo $origititle = orignititle($imagetitle);  echo '<br>';

    $f_contents = file($uploadfile1);
    $imageprefix1 = $f_contents[rand(0, count($f_contents) - 1)];

    $f_contents = file($uploadfile2);
    $imageprefix2 = $f_contents[rand(0, count($f_contents) - 1)];

    updatename($dir,$imageprefix1,$imageprefix2,$origniname,$posttitle,$format ,$origititle ,$mediaID,$path,$meta_id,$cimagename);

  }}
}

function updatename($dir,$imageprefix1,$imageprefix2,$origniname,$posttitle,$format,$origititle,$mediaID,$path,$meta_id,$cimagename)
{
   global $wpdb;

  $posttitle1 = str_replace(" ","_",$posttitle);
  $imageprefixUn1 =  str_replace(" ","_",$imageprefix1);
  $imageprefixUn2 =  str_replace(" ","_",$imageprefix2);

   $newimagname = $dir.'/'.trim($imageprefixUn1).'__'.trim($imageprefixUn2).'__'.trim($origniname).'__'.trim($posttitle1).'.'.$format;
   $newtitle = trim($imageprefix1).' '.trim($imageprefix2).' '.trim($origniname).' '.trim($posttitle) ;
  //echo '</br>';
   $imagepath = $path.'/wp-content/uploads/';
  //echo '</br>';
    $newimagname;
    echo $imagepath.$cimagename;
  rename($imagepath.$cimagename,$imagepath.$newimagname);
    echo '</br>';
  $sql = "update wp_postmeta set meta_value = '".$newimagname."'where meta_id = '".$meta_id."'";
  $result = $wpdb->query($sql);
    echo '</br>';

  $sql = "update wp_posts set post_title  = '".$newtitle."'where ID = '".$mediaID."'";
  $result = $wpdb->query($sql);
    echo '</br>';


  echo $sql1 = "select meta_value,meta_id from wp_postmeta where meta_value like '%".$cimagename."%'" ;
  $result1 = $wpdb->get_results($sql1);
  $arr = json_decode(json_encode($result1), true);
  //print_r($arr);
  $meta_values = $arr[0]['meta_value'];
   $meta_ids = $arr[0]['meta_id'];echo '<br>';
echo   $cimagename ; echo '<br>';
echo   $newimagname; echo '<br>';
  str_replace($cimagename,$newimagname,$meta_values);

  str_replace($cimagename,$newimagname,$meta_values);

  $sql2 = "update wp_postmeta set meta_value = '".$meta_values."'where meta_id = '".$meta_ids."'";
  $result = $wpdb->query($sql2);
}


function origniname($cimagename)
 {
 if(strpos($cimagename ,'__'))
 {
 $temp = str_replace("__","#",$cimagename);
 $temp2 = explode($cimagename,"#");

 if(array_count_values($temp2)>2)
 {
   return $temp2[2];
 }}
 return $cimagename ;
}


function orignititle($imagetitle1)
{
$imagetitle = str_replace(" ","_",$imagetitle1);
  //$imagetitle = str_replace("-","_",$imagetitle2);  //echo '<b>';

  if(strpos($imagetitle,'__')){
  $temp = str_replace("__","#",$imagetitle);
  $temp2 = explode($imagetitle,"#");
  if(array_count_values($temp2)>2)
  {
    return $temp2[2];
  }}
  return $imagetitle ;
}

function getsubpostname($mainpost,$metavalue)
{
    global $wpdb;
    foreach($mainpost as $key => $value)
    {
      echo $title = $key;
      $postId = implode(',',$value);
      print_r($postId);

           $sql = "SELECT p.ID,pm.meta_value,pm.meta_id,p.post_title FROM ".$wpdb->base_prefix."posts AS p LEFT JOIN ".$wpdb->base_prefix."postmeta pm ON p.ID = pm.post_id
                   WHERE pm.meta_key = '_wp_attached_file' and p.ID in (".$postId.") and (".$metavalue.")" ;

            $result = $wpdb->get_results($sql);
            $array = json_decode(json_encode($result), true);
            //changeimagename($array,$title);
            $imagename[$title]=$array;
            //print_r($imagename);
     }
    //print_r($imagename);
  return $imagename ;
}


function getpostdetails($metavalue)
  {
     global $wpdb;

    $sql = "select * from ".$wpdb->base_prefix."posts where post_status = 'publish' order by post_modified desc;" ;
    $result = $wpdb->get_results($sql);
    $array = json_decode(json_encode($result), true);
    $a1 = array();
    echo '<pre>';

    foreach($array as $key)
     {
          $content = $key['post_content'];
          $title = $key['post_title'];
          $postid = $key['ID'];
        //  $gallery = explode("=",$content);
          $subpost = getimageIdfromcontent($content,$postid);
          $array = explode(',',$subpost );
//          print_r($array);
          $main[$title]=$array;
     }
          return($main);
  }


function getimageIdfromcontent($content,$postid)
  {

    if (strpos($content,'gallery') !== false && strpos($content,'ids') !== false) {
    $ids = explode ("ids=",$content);
    $idstring = preg_replace("/[^0-9,.]/", "", $ids[1]);
     return  $idstring ;
     }

    if(strpos($content,'gallery') !== false && strpos($content,'ids') !== true) {
     $arraystring = getimagesIdfromParent($postid);
     return $arraystring ;
    }


    if(strpos($content,'gallery') !== true && strpos($content,'ids') !== true)
    {
      $arraystring = getimagesIdfromParent($postid);
      return $arraystring ;
    }


  }

function getimagesIdfromParent($postid)
{

  global $wpdb;
  $sql = 'SELECT GROUP_CONCAT(ID) as Id   FROM '.$wpdb->base_prefix.'posts where post_parent = '.$postid.' group by post_parent';
  $result = $wpdb->get_results($sql);
  $idgetfromparent = json_decode(json_encode($result), true);
  return $idgetfromparent[0][Id];

}















 ?>





 <!doctype html>
 <html lang="en">
 <head>
   <meta charset="utf-8">
   <title>Change Image Name</title>
   <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
   <script src="//code.jquery.com/jquery-1.10.2.js"></script>
   <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
   <link rel="stylesheet" href="/resources/demos/style.css">
   <script>
   $(function() {
     var firstdate = $('#firstdatepicker').datepicker({ dateFormat: 'yy-mm-dd' }).val();

   });
   </script>


   <script>

   $(function() {
     var lastdate = $('#lastdatepicker').datepicker({ dateFormat: 'yy-mm-dd' }).val();
     //$( "#lastdatepicker" ).datepicker();
   });

   </script>

 </head>
 <body style="">
   <div style="margin:auto;" >
     <br/>
     <br/>
     <br/>
     <br/>
  <form method="post" action="changeimagen.php" enctype="multipart/form-data">
    <table style="margin:auto; border:2px solid #eee; border-radius:5px; padding:50px;"><tr><td>
 <p>First Date: </td><td> <input type="text" id="firstdatepicker" name="firstdatepicker"></p>
 </td></tr><tr><td>
 <p>Last Date: </td><td><input type="text" id="lastdatepicker" name="lastdatepicker"></p>
 </td></tr><tr><td>
 <p>Text File 1 : </td><td><input type="file" name="file1" id="file1"/></p>
 </td></tr><tr><td>
 <p>Text File 2 : </td><td><input type="file" name="file2" id="file2"/></p>
 </td></tr>
 <tr><td>&nbsp;</td></tr><tr><td>
 <input type="submit" value="Get Data" name="data"/>
 </td></tr>
 </form>
 </div>
 </body>
 </html>
