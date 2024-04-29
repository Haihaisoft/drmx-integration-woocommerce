<?php
session_start();
require '../../../wp-load.php';
error_reporting(E_ALL & ~E_DEPRECATED);

$flag	= 0;
// Get the yourproductid set in the license Profile.
$PIDs	= explode('-', $_SESSION['ProductID']);

// Get integration parameters
define( 'DRMX_ACCOUNT', 		get_option('drmx_account'));
define( 'DRMX_AUTHENTICATION', 	get_option('drmx_authentication'));
define( 'DRMX_GROUPID', 		get_option('drmx_groupid'));
define( 'DRMX_RIGHTSID', 		get_option('drmx_rightsid'));
define( 'WSDL', 				get_option('drmx_wsdl'));
define( 'DRMX_BINDING', 		get_option('drmx_binding'));

$client = new SoapClient(WSDL, array('trace' => false));

if($_POST){
	$username 	= $_REQUEST["username"];
	$pwd 		= $_REQUEST["password"];
	$user 		= get_user_by('login', $username); 
	$userid 	= $user->ID;
	$user_pass 	= $user->user_pass;
	$userEmail	= $user->user_email;

	if (!wp_check_password($pwd, $user_pass)) {
		echo "<SCRIPT language=JavaScript>location='drmx_LicError.php?message=Your Account or Password is incorrect! <br><br>Please revisit the course!<br>&error=null'</SCRIPT>";
		exit;
	}
}else{
	$userid 	= get_current_user_id();
	$user 		= get_user_by( 'id', $userid );
	$username 	= $user->user_login;
	$userEmail 	= $user->user_email;
}

/********* Business logic verification ***********/

/****** Connect database configuration *******/
$dbcon=mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($dbcon -> connect_errno) {
	echo "Failed to connect to MySQL: " . $dbcon -> connect_error;
	exit();
}
mysqli_set_charset ($dbcon,'utf8');

// Get website db table prefix
$table_prefix = $wpdb->prefix;

//Query order based on user ID 
$order_query = mysqli_query($dbcon,"SELECT post_id FROM ".$table_prefix."postmeta WHERE meta_key= '_customer_user' AND meta_value = '" .$userid. "'");

while($order_result = mysqli_fetch_array($order_query,MYSQLI_ASSOC)){

	$PostID = $order_result['post_id'];
	//Get order_item_id according to post_id 
	
	$order_item_id_query = mysqli_query($dbcon,"SELECT order_item_id FROM ".$table_prefix."woocommerce_order_items WHERE order_item_type = 'line_item' AND order_id = '" .$PostID. "'");
	
	while($order_item_id_result = mysqli_fetch_array($order_item_id_query,MYSQLI_ASSOC)){
		$order_item_id = $order_item_id_result['order_item_id'];
		//Get product ID according to order id 
		$product_query = mysqli_query($dbcon,"SELECT meta_value FROM ".$table_prefix."woocommerce_order_itemmeta WHERE meta_key = '_product_id' AND order_item_id = '" .$order_item_id. "'");
		$product_result = mysqli_fetch_array($product_query);
		$product_id = $product_result['meta_value'];
		// Compare whether the product ID purchased by the user is equal to the product ID set in the license Profile 
		foreach ($PIDs as $ProductID) {
			if($product_id == $ProductID){
				// Check order status 
				$status_query = mysqli_query($dbcon,"SELECT post_status FROM ".$table_prefix."posts WHERE ID = ".$PostID);
				$status_result = mysqli_fetch_array($status_query,MYSQLI_ASSOC);
				$order_status = $status_result['post_status'];
	
				//Check if the order status is completed 
				$flag = "-1"; //订单状态未知
				
				if($order_status == "wc-completed" ){ //
					// If the username is not exists, call 'AddNewUser' to add user.
					if(checkUserExists($client, $username) == "False"){
						$addNewUserResult = addNewUser($client, $username, $userEmail);
						$info = $addNewUserResult;
						$flag = 1;
					}
	
					// check user is revoked
					/*if(checkUserIsRevoked($client, $username)){
						$errorInfo = 'Username: '.$username.' is revoked.';
						header("location:drmx_LicError.php?error=".$errorInfo."&message=".$message);
						exit;
					}*/
					
					/*** Obtaining course duration ***/
					$lp_duration = 180;

					/*** Automatically update license permissions for users based on duration of the course ****/
					$updateRightResult = updateRight($client, $lp_duration, $userEmail);
	
					if($updateRightResult == '1'){
						
						/*****After the License Rights is updated, perform the method of obtaining the license****/
						$licenseResult = getLicense($client, $username);
						$license = $licenseResult->getLicenseRemoteToTableWithVersionResult;
						$message = $licenseResult->Message;
	
						if(stripos($license, '<div id="License_table_DRM-x4" style="display:none;">' )  === false ){
							header('location: drmx_LicError.php?error='.$license.'&message='.$message);
							exit;
						}
						/*****After obtaining the license, store the license and message through the session, and then direct to the licstore page******/
						$_SESSION['license'] = $license;
						$_SESSION['message'] = $message;
						
						header('location: licstore.php');
						$flag = 1;
						$info = "Getting license...";
						exit;
					}else{
						$info = $updateRightResult;
						$flag = 1;
						break;
					}
				}
			}
		}
	}
}

if($flag == 0) $info = "You did not purchase the product. ";
if($flag == "-1") $info = "The order is not completed.";

mysqli_close($dbcon);


/***** End of business logic verification ******/

/********DRM-X 4.0 functions********/
function getIP(){
    static $realip;
    if (isset($_SERVER)){
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
            $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $realip = $_SERVER["HTTP_CLIENT_IP"];
        } else {
            $realip = $_SERVER["REMOTE_ADDR"];
        }
    } else {
        if (getenv("HTTP_X_FORWARDED_FOR")){
            $realip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("HTTP_CLIENT_IP")) {
            $realip = getenv("HTTP_CLIENT_IP");
        } else {
            $realip = getenv("REMOTE_ADDR");
        }
    }
    return $realip;
}

function checkUserExists($client, $username) {
    $CheckUser_param = array(
        'UserName'				=> $username,
        'AdminEmail'			=> DRMX_ACCOUNT,
        'WebServiceAuthStr'		=> DRMX_AUTHENTICATION,
    );
    
	$CheckUser = $client->__soapCall('CheckUserExists', array('parameters' => $CheckUser_param));
    return $CheckUser->CheckUserExistsResult;
}

function addNewUser($client, $username, $userEmail){
	$add_user_param = array(
		'AdminEmail'		=> DRMX_ACCOUNT,
		'WebServiceAuthStr'	=> DRMX_AUTHENTICATION,
		'GroupID'			=> DRMX_GROUPID,
		'UserLoginName'		=> $username,
		'UserPassword'		=> 'N/A',
		'UserEmail'			=> $userEmail,
		'UserFullName'		=> 'N/A',
		'Title'				=> 'N/A',
		'Company'			=> 'N/A',
		'Address'			=> 'N/A',
		'City'				=> 'N/A',
		'Province'			=> 'N/A',
		'ZipCode'			=> 'N/A',
		'Phone'				=> 'N/A',
		'CompanyURL'		=> 'N/A',
		'SecurityQuestion'	=> 'N/A',
		'SecurityAnswer'	=> 'N/A',
		'IP'				=> getIP(),
		'Money'				=> '0',
		'BindNumber'		=> DRMX_BINDING,
		'IsApproved'		=> 'yes',
		'IsLockedOut'		=> 'no',
	);

	$add_user = $client->__soapCall('AddNewUser', array('parameters' => $add_user_param));
	return $add_user->AddNewUserResult;
}

function updateRight($client, $lp_duration, $userEmail){
	$beginDate = date("Y/m/d", strtotime("-2 days"));
	$ExpirationDate = date("Y/m/d", strtotime("+1 year"));

	$updateRight_param = array(
		'AdminEmail'				=> DRMX_ACCOUNT,
		'WebServiceAuthStr'			=> DRMX_AUTHENTICATION,
		'RightsID'					=> DRMX_RIGHTSID,
		'Description'				=> "Courses Rights (Please don't delete)",
		'PlayCount'					=> "-1",
		'BeginDate'					=> $beginDate,
		'ExpirationDate'			=> $ExpirationDate,
		'ExpirationAfterFirstUse'	=> $lp_duration,
		'RightsPrice'				=> "0",
		'AllowPrint'				=> "False",
		'AllowClipBoard'			=> "False",
		'AllowDoc'					=> "False",
		'EnableWatermark'			=> "True",
		'WatermarkText'				=> $userEmail." ++username",
		'WatermarkArea'				=> "1,2,3,4,5,",
		'RandomChangeArea'			=> "True",
		'RandomFrquency'			=> "12",
		'EnableBlacklist'			=> "True",
		'EnableWhitelist'			=> "True",
		'ExpireTimeUnit'			=> "Day",
		'PreviewTime'				=> 3,
		'PreviewTimeUnit'			=> "Day",
		'PreviewPage'				=> 3,
		'DisableVirtualMachine'		=> 'True',
	);
	$update_Right = $client->__soapCall('UpdateRightWithDisableVirtualMachine', array('parameters' => $updateRight_param));
	return $update_Right->UpdateRightWithDisableVirtualMachineResult;
}

function checkUserIsRevoked($client, $username){
	$CheckUserIsRevoked = array(
		'AdminEmail'         => DRMX_ACCOUNT,
		'WebServiceAuthStr'  => DRMX_AUTHENTICATION,
		'UserLoginName'      => $username,
	);
	$CheckUserIsRevokedResult = $client->__soapCall('CheckUserIsRevoked', array('parameters' => $CheckUserIsRevoked));
	return $CheckUserIsRevokedResult->CheckUserIsRevokedResult;
}

function getLicense($client, $username){
	$param = array(
		'AdminEmail'         => DRMX_ACCOUNT,
		'WebServiceAuthStr'  => DRMX_AUTHENTICATION,
		'ProfileID'          => $_SESSION["ProfileID"],
		'ClientInfo'         => $_SESSION["ClientInfo"],
		'RightsID'           => DRMX_RIGHTSID,
		'UserLoginName'      => $username,
		'UserFullName'       => 'N/A',
		'GroupID'            => DRMX_GROUPID,
		'Message'            => 'N/A',
		'IP'                 => getIP(),
		'Platform'           => $_SESSION["Platform"],
		'ContentType'        => $_SESSION["ContentType"],
		'Version'            => $_SESSION["Version"],
	);
	
	/*****Obtain license by calling getLicenseRemoteToTableWithVersion******/
	$result = $client->__soapCall('getLicenseRemoteToTableWithVersion', array('parameters' => $param));
	return $result;
}

?>

<!DOCTYPE html>
<html>
<head>
 <meta http-equiv="content-type" content="text/html; charset=UTF-8">
 <title>Login and obtain license</title>
 <link rel="stylesheet" type="text/css" href="public/css/login-style.css" />
</head>

<body>
	<div class="login-wrap">
       	<div class="login-head">
          	<div align="center"><img src="public/images/logo.png" alt=""></div>
       	</div>
		<div class="login-cont">
			<div id="btl-login-error" class="btl-error">
				<div class="black">
					<?php 
						echo esc_attr($info);
					?>
				</div>
			</div>
			<div class="login-foot">
				<div class="foot-tit">Other options</div>
				<div class="foot-acts">
					<a class="link-reg" href="<?php echo esc_attr(site_url()); ?>" target="_blank">Help?</a>
					<a class="link-get-pwd" href="<?php echo esc_attr(site_url()); ?>" target="_blank">Buy Course</a>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
