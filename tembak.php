<?php
/**
 *Name:    XlRequest
 *Author:  Adipati arya
 *aryaadipati2@gmail.com
 *@adipati
 *
 * Added Awesomeness: Adipati arya
 *
 * Created:  11.10.2017
 *
 * Description:  Modified auth system based on Guzzle with extensive customization. This is basically what guzzle should be.
 * Original Author name has been kept but that does not mean that the method has not been modified.
 *
 * Requirements: PHP5 or above
 * @package		Xlrequest
 * @author		aryaadipati2@gmail.com
 * @link		http://sshcepat.com/xl
 * @filesource	https://github.com/adipatiarya/XLRequest
 */
 
require 'XlRequest.php';

function service($str) {
	
	switch ((int) $str) {
		
		case 1: return 8210441;
		break;
		
		case 2: return 8210883;
		break;
		
		case 3: return 8210882;
		break;
		
		default:
		
	}
}

if (isset($_POST['msisdn']) && isset($_POST['passwd']) && isset($_POST['reg']))
{
	$msisdn = $_POST['msisdn'];
	$passwd = $_POST['passwd'];
	$idService = service($_POST['reg']);
	
	if( !empty($_POST['manual']) )
	{
		$idService = $_POST['manual'];
	}
	try
	{
		$request = new XlRequest();
		$login = $request->login($msisdn,$passwd);
		
		if ($login !== false) {
			$fil = fopen('count_file.txt', r);
		    $dat = fread($fil, filesize('count_file.txt')); 
		    $dat+1;
		    fclose($fil);
		    $fil = fopen('count_file.txt', w);
		    fwrite($fil, $dat+1);
		    fclose($fil);
		    $register = $request->register($idService);
		    if (!isset($register->responseCode)) {
				
				echo $register->purchase_confimation->package_purchased_confirmation_thankyou_text;
		    }
			else
			{
				echo $register->message;
			}
		}
		else {
			
			echo "Login failed try againt";
			return;
		}
			
	}
	catch(Exception $e) {}
		
} else {
	   echo "Access Denied";
}
?>