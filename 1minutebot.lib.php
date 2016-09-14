<?php
/**
 * 1MinuteBot PHP Class
 *
 * @category  Bot
 * @package   1MinuteBot
 * @copyright Copyright (c) 2016
 * @link      https://www.1minutebot.com
 * @version   1.0-master
 */

class OMBotUser {
	
    /**
     * Bot's App ID
     * @var string
     */
    private $app_id;
    
    /**
     * Bot's API Key
     * @var string
     */
    private $api_key;
    
    /**
     * User's attributes
     * @var string
     */
    private $attributes;
    public $user_id;
    public $first_name;
    
    /**
     * @param string $app_id
     * @param string $api_key
     * @param int $user_id
     */
	public function __construct($app_id, $api_key, $user_id=false)
	{
		$this->app_id = $app_id;
		$this->api_key = $api_key;
		if($user_id) {
			$this->getUser($user_id);
		}
	}
	
    /**
     * @param string $action
     * @param string $payload
     * @return array
     */
	public function doRequest($action, $payload)
	{
		/* This method does not require CURL to be installed on the machine */
		$url = 'https://api.1minutebot.com/' . $this->app_id . ':' . $this->api_key . '/' . $action;
		$options = array(
		    'http' => array(
		        'header'  => "Content-type: text/json\r\n",
		        'method'  => 'POST',
		        'content' => json_encode($payload)
		    )
		);
		$context  = stream_context_create($options);
		$result = json_decode(file_get_contents($url, false, $context), true);
		return $result;
	}
	
    /**
     * @param array $user
     */
	public function setUserData($user)
	{
		//$this->..... = $user['....'];
		$this->first_name = $user['first_name'];
		$this->user_id = $user['user_id'];
		$this->attributes = $user['attributes'];
	}
	
    /**
	 * Change current user
     * @param string $user_id
     */
	public function getUser($user_id)
	{
		$user = $this->doRequest('getUser', array('user_id' => $user_id));
		if($user['success']) {
			$this->setUserData($user['data']);
			return true;
		}
		else
			return false;
	}
	
    /**
	 * [Shortcut] Get attribute from an user
     * @param string $attribute_name
     */
	public function getAttr($attribute_name)
	{
		return $this->getAttribute($attribute_name);
	}
	
    /**
	 * Get attribute from an user
     * @param string $attribute_name
     */
	public function getAttribute($attribute_name)
	{
		return isset($this->attributes[$attribute_name]) ? $this->attributes[$attribute_name] : false;
	}
	
	
    /**
	 * [Shortcut] Set attribute to an user
     * @param string $attribute_name
     * @param string $attribute_value
     */
	public function setAttr($attribute_name, $attribute_value)
	{
		return $this->setAttribute($attribute_name, $attribute_value);
	}
	
    /**
	 * Set attribute to an user
     * @param string $attribute_name
     * @param string $attribute_value
     */
	public function setAttribute($attribute_name, $attribute_value)
	{
		if($this->user_id) {
			if(strlen($attribute_value) > 0)
				$this->attributes[$attribute_name] = $attribute_value;
			else
				unset($this->attributes[$attribute_name]);
			return $this->doRequest('setAttribute', array('user_id' => $this->user_id, 'attribute_name' => $attribute_name, 'attribute_value' => $attribute_value));
		}
		return false;
	}
	
    /**
	 * Send Text to an user
     * @param string $text
     */
	public function sendText($text)
	{
		if($this->user_id)
			return $this->doRequest('sendText', array('user_id' => $this->user_id, 'text' => $text));
		return false;
	}
	
    /**
	 * Send Image to an user
     * @param string $caption
     * @param string $image_url
     */
	public function sendImage($caption, $image_url)
	{
		if($this->user_id)
			return $this->doRequest('sendImage', array('user_id' => $this->user_id, 'caption' => $caption, 'image_url' => $image_url));
		return false;
	}
	
    /**
	 * Send File to an user
     * @param string $caption
     * @param string $file_url
     */
	public function sendFile($caption, $file_url)
	{
		if($this->user_id)
			return $this->doRequest('sendFile', array('user_id' => $this->user_id, 'caption' => $caption, 'file_url' => $file_url));
		return false;
	}
	
    /**
	 * Send Buttons to an user
     * @param string $text
     * @param array of buttons $buttons {'caption': '<caption>', 'url': '<url>' or 'callback': '<callback>'}
     */
	public function sendButtons($text, $buttons)
	{
		if($this->user_id)
			return $this->doRequest('sendButtons', array('user_id' => $this->user_id, 'text' => $text, 'buttons' => $buttons));
		return false;
	}
	
    /**
	 * Send Elements to an user
     * @param array $elements
     */
	public function sendElements($elements)
	{
		if($this->user_id)
			return $this->doRequest('sendElements', array('user_id' => $this->user_id, 'elements' => $elements));
		return false;
	}
	
}

class OMBotWebhook {
	
    /**
     * Bot's App ID
     * @var string
     */
    private $app_id;
    
    /**
     * Bot's API Key
     * @var string
     */
    private $api_key;
    
    /**
     * Callback received
     * @var boolean
     */
    public $webhook_received;
    
    /**
     * User data
     * @var OMBotUser
     */
    public $user;
    
    /**
     * Message type
     * @var bool
     */
    public $first_interaction;
    
    /**
     * Message type
     * @var string
     */
    public $message_type;
    
    /**
     * Message data
     * @var array
     */
    public $message;
	
    /**
     * @param string $app_id
     * @param string $api_key
     */
	public function __construct($app_id=false, $api_key=false)
	{
		$this->app_id = $app_id;
		$this->api_key = $api_key;
		$this->handleWebhook();
	}
	
    /**
	 * Handle the eventual Webhook
     */
	public function handleWebhook()
	{
		$this->webhook_received = false;
		$input = file_get_contents('php://input');
		$payload = json_decode($input, true);
		if($payload && isset($payload['message_type'])) {
			$this->webhook_received = true;
			$this->message_type = $payload['message_type'];
			$this->first_interaction = $payload['first_interaction'];
			$this->message = $payload['message'];
			$this->user = new OMBotUser($this->app_id, $this->api_key);
			$this->user->setUserData($payload['user']);
			try {
				switch($this->message_type) {
					case 'first_message':
						//TODO: If firstmessage not implemented, fallback to text/callback/files
						$this->onFirstMessage($this->user, $this->message['text']);
					break;
					case 'text':
						$this->onTextReceived($this->user, $this->message['text']);
					break;
					case 'callback':
						$this->onCallbackReceived($this->user, $this->message['callback']);
					break;
					case 'files':
						$this->onFilesReceived($this->user, $this->message['files'], $this->message['text']);
					break;
				}
			}
			catch(Exception $e) {
				//Only if user is admin!
				$this->user->sendText('Exception: ' . $e);
			}
		}
	}
	
	/**  ____      _ _ _                _      _____                 _       
	 *  / ___|__ _| | | |__   __ _  ___| | __ | ____|_   _____ _ __ | |_ ___ 
	 * | |   / _` | | | '_ \ / _` |/ __| |/ / |  _| \ \ / / _ \ '_ \| __/ __|
	 * | |__| (_| | | | |_) | (_| | (__|   <  | |___ \ V /  __/ | | | |_\__ \
	 *  \____\__,_|_|_|_.__/ \__,_|\___|_|\_\ |_____| \_/ \___|_| |_|\__|___/
     */
	
    /**
	 * onFirstMessage
     * @param string $text
     */
	public function onFirstMessage($user, $text)
	{
		/* Extend the class to implement this event method */
	}
	
    /**
	 * onTextReceived
     * @param string $text
     */
	public function onTextReceived($user, $text)
	{
		/* Extend the class to implement this event method */
	}
	
    /**
	 * onCallbackReceived
     * @param mixed $callback
     */
	public function onCallbackReceived($user, $callback)
	{
		/* Extend the class to implement this event method */
	}
	
    /**
	 * onFilesReceived
     * @param mixed $files
     * @param string $text
     */
	public function onFilesReceived($user, $files, $text)
	{
		/* Extend the class to implement this event method */
	}
	
}

?>
