<?php
/*
	Plugin Name: Wp Restricted
	Plugin URI: https://neeraj2855.wordpress.com/
	Description: This plugin is restricted all ip except one ip . if you want open wp admin with particular single IP. 
	Version: 0.2
	Author: Neeraj
	Author URI: https://neeraj2855.wordpress.com/
*/
?>
<?php
add_action('init', 'wpr_banned_ip_address');
class wpr_restricted_with_ip
{
	public function __construct()
	{
		$this->wpr_restricted_init();		
	}
	public function wpr_restricted_init()
	{
		// HOOK CALL FOR MANAGE SECTION IN SETTING
		add_action( 'admin_menu', array($this, 'wpr_restricted_admin_menu' ) );
		
	}
	public function wpr_restricted_admin_menu()
	{
		add_options_page('Wp Restricted','Wp Restricted','manage_options','wprestricted_manage',
		array($this,'wpr_restricted_manage_fun'));
	}
	public function wpr_restricted_manage_fun()
	{
		if(isset($_POST['submit_ip']) && !empty($_POST['wpr_ip_res']))
		{
			if(!filter_var($_POST['wpr_ip_res'], FILTER_VALIDATE_IP))
			{
				echo "Ip address is not valid";
			}
			else
			{
				update_option('wpr_restricted_ip',$_POST['wpr_ip_res']);
			}
			
		}
		$getIp = get_option('wpr_restricted_ip');
		?>
			<table class="widefat">
				<thead>
					<tr>
						<th><?php _e('Your Details', 'wpr-restricted'); ?></th>
						<th><?php _e('Value', 'wpr-restricted'); ?></th>
					</tr>
				</thead>
				<tr>
					<td><?php _e('IP', 'wpr-restricted'); ?>:</td>
					<td><strong><?php echo wpr_getip_address(); ?></strong></td>
				</tr>
				<tr class="alternate">
					<td><?php _e('Host Name', 'wpr-restricted'); ?>:</td>
					<td><strong><?php echo @gethostbyaddr(wpr_getip_address()); ?></strong></td>
				</tr>
				<tr>
					<td><?php _e('User Agent', 'wpr-restricted'); ?>:</td>
					<td><strong><?php echo $_SERVER['HTTP_USER_AGENT']; ?></strong></td>
				</tr>
				<tr class="alternate">
					<td><?php _e('Site URL', 'wpr-restricted'); ?>:</td>
					<td><strong><?php echo get_option('home'); ?></strong></td>
				</tr>
			</table>
			<form name="sq" method="post" action="">
				<label>Enter Ip :</label> <input style="width:290; height:40px;" type="text" name="wpr_ip_res" value="<?php echo $getIp ?>">
				<input type="submit" name="submit_ip" class="button button-primary button-large" style="margin:6px;" value="Save">
			</form>
		<?php
	}
}
$wpObj 	= new wpr_restricted_with_ip;

function wpr_banned_ip_address() {

	$current_url="//".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	if(preg_match("~\wp-admin\b~",$current_url) || preg_match("~\wp-login\b~",$current_url) || preg_match("~\wp-login.php\b~",$current_url))
	{

		$getIp = get_option('wpr_restricted_ip');
		if(!empty($getIp))
		{
			if(wpr_getip_address() != $getIp)
			{ ?>
				<script type="text/javascript"> 
				window.location = "<?php echo site_url() ?>"; </script>
				<?php
			}
		}
	}

}

// GET IP ADDRESS FUNCTION ( http://stackoverflow.com/a/2031935 )

function wpr_getip_address(){
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
        if (array_key_exists($key, $_SERVER) === true){
            foreach (explode(',', $_SERVER[$key]) as $ip){
                $ip = trim($ip); // just to be safe

                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                    return $ip;
                }
            }
        }
    }
}

