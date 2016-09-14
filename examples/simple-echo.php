<?php

require_once('1minutebot.lib.php');

class myBot extends OMBotWebhook {
	
	function onTextReceived($user, $text) {
		$user->sendText('Hi ' . $user->first_name . ', you just sent me: ' . $text);
	}
	
}

new myBot('app_id', 'secret');

?>