<?php include('../_connect.php');?>
<?php include('../../includes/helpers/short.php');?>
<?php 
	//-------------------------- ERRORS -------------------------//
	$error_core = array('No data passed', 'API key not passed', 'Invalid API key');
	$error_passed = array('List ID not passed', 'List does not exist', 'Email address not passed', 'Subscriber does not exist');
	//-----------------------------------------------------------//
	
	//--------------------------- POST --------------------------//
	//api_key
	if(isset($_POST['api_key'])) $api_key = mysqli_real_escape_string($mysqli, $_POST['api_key']);
	else $api_key = null;
	
	//list_id
	if(isset($_POST['list_id'])) $list_id = short(mysqli_real_escape_string($mysqli, $_POST['list_id']), true);
	else $list_id = null;
	
	//email
	if(isset($_POST['email'])) $email = mysqli_real_escape_string($mysqli, $_POST['email']);
	else $email = null;
	//-----------------------------------------------------------//
	
	//----------------------- VERIFICATION ----------------------//
	//Core data
	if($api_key==null && $list_id==null)
	{
		echo $error_core[0];
		exit;
	}
	if($api_key==null)
	{
		echo $error_core[1];
		exit;
	}
	else if(!verify_api_key($api_key))
	{
		echo $error_core[2];
		exit;
	}
	
	//Passed data
	if($list_id==null)
	{
		echo $error_passed[0];
		exit;
	}
	else
	{
		//Check if list exists
		$q = 'SELECT id FROM lists WHERE id = '.$list_id;
		$r = mysqli_query($mysqli, $q);
		if (mysqli_num_rows($r) == 0) //if list does not exist, throw error
		{
			echo $error_passed[1]; 
			exit;
		}
		else //if list exists, check if email is passed into the call
		{
			//if email is not passed into the call
			if($email==null)
			{
				
			}
			else //otherwise check if the email exist inside the list
			{
				$q = 'SELECT id FROM subscribers WHERE email = "'.$email.'" AND list = '.$list_id;
				$r = mysqli_query($mysqli, $q);
				if (mysqli_num_rows($r) == 0)
				{
				    echo $error_passed[3]; 
					exit;
				}
			}
		}
	}
	//-----------------------------------------------------------//
	
	//-------------------------- QUERY --------------------------//
	
	$q = 'DELETE FROM subscribers WHERE email = "'.$email.'" AND list = '.$list_id;
	$r = mysqli_query($mysqli, $q);

	// hitting third party unsubscribe... http://o.degtrak.com/o-tslc-k04-a5e6b420707feba31a343765768f859e&cr=13?email=email@goeshere.com

	$url_to_hit = "http://o.degtrak.com/o-tslc-k04-a5e6b420707feba31a343765768f859e&cr=13?email=".$email;
	$url_to_hit_2= "http://o.degtrak.com/o-tslc-k04-40fb3a2c968dcd1a5b5caa0d1e94dd47&cr=12&email=".$email;
	$ch = curl_init($url_to_hit);
	curl_exec($ch);

	$ch2 = curl_init($url_to_hit_2);
	curl_exec($ch2);

	if ($r)
	    echo true;
	//-----------------------------------------------------------//
?>