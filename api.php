<?php

$access_token = getenv('TELEGRAM_ACCESSTOKEN');
$chat_id = getenv('TELEGRAM_CHATID');
$url = 'https://api.telegram.org/bot' . $access_token . '/getUpdates';
if ($_GET['after']) {
    $url .= '?offset=' . urlencode($_GET['after']);
}
$obj = json_decode(file_get_contents($url));
$ret = new StdClass;
$ret->messages = array();
$update_id = $_GET['after'];
foreach ($obj->result as $result) {
    if ($update_id == $result->update_id) {
        continue;
    }
    $update_id = $result->update_id;
    if (!property_exists($result, 'message')) {
        continue;
    }
    if ($result->message->chat->id != $chat_id) {
        continue;
    }
    if ($result->message->date < time() - 5 * 60) {
        continue;
    }
    $ret->messages[] = array(
        'user' => $result->message->from->username,
        'text' => $result->message->text,
    );
}
$ret->next_url = '/api.php?after=' . $update_id;
echo json_encode($ret, JSON_UNESCAPED_UNICODE);
