<?php	
session_start();

#-------preset value-------------
#time stamp for file name
date_default_timezone_set('EST');
#date('m_d_H_i_s');

#address
$AWS_address="http://cdn.sealykelvin.com/id/";

#page refresh in a hour or below seconds
$refresh_rate= 3;

#submit1 from function, is use for transition select address to show_Json
#this just name convertion
$show_Json='Submit1';


#------pre conditions------
#inista color background of json title
if(isset($titleColor)==False)
{$titleColor="";}

if (isset($_SESSION["speak"])==False){$_SESSION["speak"]="off";}
if(array_key_exists("speak",$_POST))
{
	if(array_key_exists('speak_check',$_POST))
	{
		$_SESSION["speak"]="on";
	}
	else
	{
		$_SESSION["speak"]="off";
	}
}

#when load the page and engine_addres has not created
if (isset($_SESSION["engine_address"])==false)
	{	$_SESSION["engine_address"]="";	}
#select different engine button
if (array_key_exists('Change_engine',$_POST))
	{
		unset($_POST[$show_Json]); 
		$_SESSION["engine_address"]="";			
	}
#select autoRefresh box
if(array_key_exists('autoRefresh',$_POST))
{
	if (array_key_exists('Checkbox',$_POST))
		{	$_SESSION["sec"] = $refresh_rate;	
		}
	else
		{ $_SESSION["sec"] = 360;	}
}
// echo 'refresh'.$_SESSION["sec"].'sec';

include 'View&logHeader.php';
include 'functions.php';
include 'C:\Apache24\htdocs\test\ran_func.php';

#-----address page--------
if ($_SESSION["engine_address"]=="" and (!array_key_exists($show_Json,$_POST)))
{
	echo '<div id="header">
		'.display_engine().';
		</div>';
		}
	
#-----info page ---------
if (array_key_exists('mac',$_POST) or ($_SESSION["engine_address"]!="" ) )
	{
	if (array_key_exists($show_Json,$_POST))
	{$_SESSION["engine_address"]=$_POST['mac'];	} #mac has the address of the engine from func

	#header
	echo '<div id="header">
		'.$_SESSION["engine_address"].
		'<font size=3> -------- refresh '.$_SESSION["sec"].' sec </font>
		<!--  back to address page ###################### --> 
		<form id="Change_engine" method="post" action="'.$_SERVER['PHP_SELF'].'" >
		<input type="submit" name="Change_engine" value="Select Different Engine"/>
		<input type="submit" name="refresh" value="refresh"/>
		';

		
		#auto refress check box
	echo '
		<from id="autoRefresh" method="post" action="'.$_SERVER['PHP_SELF'].'">
		';
		if ($_SESSION["sec"]==$refresh_rate) 
		{			echo '<input type="checkbox" name="Checkbox" value="enable" checked/>';		}
		else
		{			echo '<input type="checkbox" name="Checkbox" value="enable"/>';		}
	echo '<input type="submit" name="autoRefresh" value="auto refresh"/>';
	
		#speak
	echo' <from id="speak" method="post" action="'.$_SERVER['PHP_SELF'].'">	';
			if ($_SESSION["speak"]=="on") 
			{			echo '<input type="checkbox" name="speak_check" value="on" checked/>';		}
			else
			{			echo '<input type="checkbox" name="speak_check" value="on"/>';		}
		echo '<input type="submit" name="speak" value="speak"/>';
	

	echo '</div>';
		
		######################back to address page 
	
	#engine name
	$len=strlen($_SESSION["engine_address"]); #unknow reason there is extra space after engine address
	$engine_address=substr($_SESSION["engine_address"], 0, $len-2); #this delect last space 
	#json name
	$Jsons=array("status","settings","side-a","side-b","sleep-a","sleep-b");
	
	#display json in each small window
	foreach($Jsons as $window )
	{
		$address=$AWS_address.$engine_address.'/'.$window.'.json';#http address of each json
		$_SESSION[$window] = file_get_contents($address);
		$file = date('m_d_H_i_s').$window.'.json';
		if (isset($_SESSION["json_old"][$window]))
		{
			if ( $_SESSION[$window]!= $_SESSION["json_old"][$window] ) #if current json is != old json
				{
					$titleColor="red";  #make title red
					
					if($_SESSION["speak"]=="on") {
					audio_name($window);   #play audio
					}
					//echo '<div transition-delay: 1s></div>'; #each sound track is less than 0.5s long
					
				
					$_SESSION["json_old"][$window] = $_SESSION[$window]; #update old json
					$myfile = fopen("log\\".$file, "w");#save current json and update
					fwrite($myfile, $_SESSION[$window]);
					fclose($myfile);
				}
		}
		else
		{$_SESSION["json_old"][$window]=$_SESSION[$window];
		$titleColor="";}

		#display current window
		echo '<div id="'.$window.'">
		<font size="4"><span style="background-color:'.$titleColor.'">'.$window.'</span></font>
		<div id="json_box">'.$_SESSION[$window].'</div>
		
		
		
		</div>';
		
		#play audio is changed
		
		
		
		#store title color to default blank
		$titleColor="";
	
	 }
	
	}
echo '<div id="footer">
		This is footer
		</div>';
		

	
echo '</body>';
		
?>
