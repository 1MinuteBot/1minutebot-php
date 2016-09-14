# 1MinuteBot PHP Library
With this [1MinuteBot](https://www.1minutebot.com) PHP Library you can easily create a Webhook to receive messages from your Telegram/Facebook bot (using the ```OMBotWebhook``` class) and send text, buttons, images and files to your users (using the ```OMBotUser``` class).

## Installation

Just download and ```require_once('1minutebot.lib.php')```

## Webhook Class

Every time an user send a message to your bot, 1MinuteBot servers will send a request to your webhook page.

![webhook explaination](https://www.1minutebot.com/images/examples/webhook_howto.png "webhook explaination")

### Integration

* To implement the Webhook with an **object-oriented approach**, you will need to extend the OMBotWebhook class and override the event methods with something like this:

```php
class MyBot extends OMBotWebhook
{
	protected function onTextReceived($user, $text) {
		$user->sendText('This is a reply to your message: ' . $text);
	}
}

new MyBot('<app_id>', '<api_key>');
```

* If you insted prefer to use a **procedural approach** you can always write something like this:
```php
$bot = new OMBotWebhook('<app_id>', '<api_key>');

if($bot->webhook_received) {
	if($bot->message_type == 'text') {
  		$bot->user->sendText('This is a reply to your message: ' . $bot->message['text']);
	}
}
```

### Available events

<table>
	<tr>
		<td></td>
		<td align="center"><b>Object-oriented approach</b></td>
		<td align="center" colspan="2"><b>Procedural approach</b></td>
	</tr>
	<tr>
		<td><b>Event</b></td>
		<td><b>Method to override</b></td>
		<td><b>Value of <code>message_type</code></b></td>
		<td><b>Variables in <code>message</code></b></td>
	</tr>
	<tr>
		<td>First message</td>
		<td><code>onFirstMessage($user, $text)</code></td>
		<td><code>first_message</code></td>
		<td><code>text</code></td>
	</tr>
	<tr>
		<td>Text message</td>
		<td><code>onTextReceived($user, $text)</code></td>
		<td><code>text</code></td>
		<td><code>text</code></td>
	</tr>
	<tr>
		<td>Button callback</td>
		<td><code>onCallbackReceived($user, $callback)</code></td>
		<td><code>callback</code></td>
		<td><code>callback</code></td>
	</tr>
	<tr>
		<td>File(s) received</td>
		<td><code>onFilesReceived($user, $files, $text)</code></td>
		<td><code>files</code></td>
		<td><code>files</code>, <code>text</code></td>
	</tr>
</table>

## User Class
You can find an ```OMBotUser``` object inside the ```user``` variable of the ```OMBotWebhook``` class or instantiate a new one like this:

```php
$user = new OMBotUser('<app_id>', '<api_key>', '<user_id>');
```

You can call the following methods on your ```OMBotUser``` object:
* **sendText($text)** - Send a text message to the user.
* **sendButtons($text, $buttons)** - Send a text message with some [buttons](/README.md#buttons-data-structure) to the user.
* **sendElements($elements)** - Send a text message with some [elements](/README.md#elements-data-structure) to the user.
* **sendImage($image_url, $caption)** - Send an image with an optional caption to the user.
* **sendFile($file_url, $caption)** - Send a file with an optional caption to the user.
* **getAttribute($attribute_name)** - Get the attribute `$attribute_name` from the selected user.
* **setAttribute($attribute_name, $attribute_value)** - Set the value `$attribute_value` for the attribute `$attribute_name`. If `$attribute_value` is empty, the attribute will be unset.

### Buttons data structure
It is an array which contains 1 to 3 buttons.

Each button contains a `caption` and either an `url` or a `callback`.

**Example**
```php
$buttons = array(
	array('caption' => 'Buy on our website', 'url' => 'https://www.google.it'),
	array('caption' => 'Buy in messenger', 'callback' => 'buy_item_12345')
);
$user->sendButtons('What do you want to do?', $buttons);
```
![buttons example](https://www.1minutebot.com/images/examples/all_buttons.png "buttons example")

### Elements data structure
It is an array which contains 1 to 10 elements.

Each element contains a `title`, a `subtitle`, an `image_url` and an array of [`buttons`](/README.md#buttons-data-structure).

**Example**
```php
$elements = array(
	array(
		'title' => 'Classic White T-shirt',
		'subtitle' => 'Soft white cotton t-shirt is back in style',
		'image_url' => 'http://petersapparel.parseapp.com/img/item100-thumb.png',
		'buttons' => array(
			array('caption' => 'Show on website', 'url' => 'https://www.google.it/?thirt_12345'),
			array('caption' => 'Buy now', 'callback' => 'buy_shirt_white_12345')
		)
	),
	array(
		'title' => 'Classic Gray T-shirt',
		'subtitle' => 'Soft gray cotton t-shirt is back in style',
		'image_url' => 'http://petersapparel.parseapp.com/img/item101-thumb.png',
		'buttons' => array(
			array('caption' => 'Show on website', 'url' => 'https://www.google.it/?thirt_54321'),
			array('caption' => 'Buy now', 'callback' => 'buy_shirt_gray_54321')
		)
	)
);
$user->sendElements($elements);
```
![elements example](https://www.1minutebot.com/images/examples/all_elements.png "elements example")
