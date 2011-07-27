<?php
require_once('dreamhost.class.php');

define('API_KEY','YOURAPIKEYHERE');

$dh = new Dreamhost(API_KEY);
$dh->format('xml');  // or json, tab, etc

/*
// send a command with no parameters
header ("Content-Type:text/xml"); 
$command = 'account-domain_usage';
echo $dh->api($command);
exit;
*/

/*
// send a command with parameters
header ("Content-Type:text/xml"); 
$command = 'dreamhost_ps-list_usage';
echo $dh->api($command,array('ps'=>'ps99999'));
exit;
*/

// Play with a bunch of commands, to test out the API
$command_list = array('account-domain_usage','account-list_accounts','account-status','account-user_usage','announcement_list-add_subscriber','announcement_list-list_lists','announcement_list-list_subscribers','announcement_list-post_announcement','announcement_list-remove_subscriber','api-list_accessible_cmds','api-list_keys','dns-add_record','dns-list_records','dns-remove_record','domain-list_domains','domain-list_registrations','dreamhost_ps-add_ps','dreamhost_ps-list_images','dreamhost_ps-list_pending_ps','dreamhost_ps-list_ps','dreamhost_ps-list_reboot_history','dreamhost_ps-list_settings','dreamhost_ps-list_size_history','dreamhost_ps-list_usage','dreamhost_ps-reboot','dreamhost_ps-remove_pending_ps','dreamhost_ps-remove_ps','dreamhost_ps-set_settings','dreamhost_ps-set_size','mail-add_filter','mail-list_filters','mail-remove_filter','mysql-add_hostname','mysql-add_user','mysql-list_dbs','mysql-list_hostnames','mysql-list_users','mysql-remove_hostname','mysql-remove_user','oneclick-destroy_advanced','oneclick-destroy_easy','oneclick-install_advanced','oneclick-install_easy','oneclick-list_advanced','oneclick-list_easy','oneclick-list_settings','oneclick-set_settings','oneclick-upgrade','oneclick-upgrade_all','services-flvencoder','services-progress','user-list_users','user-list_users_no_pw');
echo '<form action="" method="GET">';
echo '<select name="form_command">';
foreach ($command_list as $command_item)
{
  if ($command_item==$_GET['form_command']) $selected='selected="selected"'; else $selected='';
  echo '<option value="'.$command_item.'"'.$selected.'>'.$command_item.'</option>';
}
echo '</select>';
echo '&nbsp;&nbsp;<input type="submit" value=" Do It " />';
echo "&nbsp;&nbsp; -- Commands that require parameters won't work (for safety)";
echo '</form>';

if ($_GET['form_command'])
{
  $command = $_GET['form_command'];  
  $xml = simplexml_load_string($dh->api($command));
  XMLtoTable($xml);
}
?>