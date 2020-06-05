<?php

/**
*
* @package phpBB Extension - Oxpus Downloads
* @copyright (c) 2002-2020 OXPUS - www.oxpus.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace oxpus\dlext\notification;

/**
* Download Extension notifications class
* This class handles notifications for new downloads
*
* @package notifications
*/
class capprove extends \phpbb\notification\type\base
{
	/**
	* Get notification type name
	*
	* @return string
	*/
	public function get_type()
	{
		return 'oxpus.dlext.notification.type.capprove';
	}

	/**
	* Notification option data (for outputting to the user)
	*
	* @var bool|array False if the service should use it's default data
	* 					Array of data (including keys 'id', 'lang', and 'group')
	*/
	public static $notification_option = [
		'lang'	=> 'DL_NOTIFY_TYPE_CAPPROVE',
		'group'	=> 'DL_NOTIFICATIONS_MOD',
	];

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\user_loader */
	protected $user_loader;

	/** @var \phpbb\controller\helper */
	protected $helper;

	protected $dlext_main;

	public function set_config(\phpbb\config\config $config)
	{
		$this->config = $config;
	}

	public function set_user_loader(\phpbb\user_loader $user_loader)
	{
		$this->user_loader = $user_loader;
	}

	public function set_helper(\phpbb\controller\helper $helper)
	{
		$this->helper	= $helper;
	}

	public function set_dl_auth($dlext_main)
	{
		$this->dlext_main	= $dlext_main;
	}

	/**
	* Is this type available to the current user (defines whether or not it will be shown in the UCP Edit notification options)
	*
	* @return bool True/False whether or not this is available to the user
	*/
	public function is_available()
	{
		$access_cat = [];
		$access_cat = $this->dlext_main->full_index(0, 0, 0, 2);
		
		return (!empty($access_cat)) ? true : false;
	}

	/**
	* Get the id of the notification
	*
	* @param array $data The data for the updated rules
	* @return int Id of the notification
	*/
	public static function get_item_id($data)
	{
		return $data['dl_id'];
	}

	/**
	* Get the id of the parent
	*
	* @param array $data The data for the updated rules
	* @return int Id of the parent
	*/
	public static function get_item_parent_id($data)
	{
		return 0;
	}

	/**
	* Find the users who will receive notifications
	*
	* @param array $data The type specific data for the updated rules
	* @param array $options Options for finding users for notification
	* @return array
	*/
	public function find_users_for_notification($data, $options = [])
	{
		$this->user_loader->load_users($data['user_ids']);
		return $this->check_user_notification_options($data['user_ids'], $options);
	}


	/**
	* Get the user's avatar
	*/
	public function get_avatar()
	{
		return 0;
	}
	/**
	* Get the HTML formatted title of this notification
	*
	* @return string
	*/
	public function get_title()
	{
		return $this->language->lang('DL_NOTIFY_CAPPROVE', $this->get_data('description'));
	}

	/**
	 * Get the HTML formatted reference of the notification
	 *
	 * @return string
	 */
	public function get_reference()
	{
		return '';
	}

	/**
	* Get email template
	*
	* @return string|bool
	*/
	public function get_email_template()
	{
		return '@oxpus_dlext/downloads_approve_comment';
	}
	/**
	* Get email template variables
	*
	* @return array
	*/
	public function get_email_template_variables()
	{
		return [
			'CATEGORY'		=> strip_tags(htmlspecialchars_decode($this->get_data('cat_name'))),
			'DOWNLOAD'		=> strip_tags(htmlspecialchars_decode($this->get_data('description'))),
			'U_APPROVE'		=> generate_board_url(true) . $this->helper->route('oxpus_dlext_mcp_capprove'),
			'U_DOWNLOAD'	=> generate_board_url(true) . $this->helper->route('oxpus_dlext_details', ['view' => 'comment', 'action' => 'view', 'df_id' => $this->get_data('df_id')], false),
		];
	}

	/**
	* Get the url to this item
	*
	* @return string URL
	*/
	public function get_url()
	{
		return $this->helper->route('oxpus_dlext_mcp_capprove');
	}

	/**
	 * Users needed to query before this notification can be displayed
	 *
	 * @return array Array of user_ids
	 */
	public function users_to_query()
	{
		return [];
	}

	/**
	* Function for preparing the data for insertion in an SQL query
	* (The service handles insertion)
	*
	* @param array $data The data for the new download
	* @param array $pre_create_data Data from pre_create_insert_array()
	*
	*/
	public function create_insert_array($data, $pre_create_data = [])
	{
		$this->set_data('user_ids', $data['user_ids']);
		$this->set_data('df_id', $data['df_id']);
		$this->set_data('dl_id', $data['dl_id']);
		$this->set_data('cat_name', $data['cat_name']);
		$this->set_data('description', $data['description']);
		parent::create_insert_array($data, $pre_create_data);
	}
}