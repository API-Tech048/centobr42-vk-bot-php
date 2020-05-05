<?php
include "config.php";
if (!isset($_REQUEST)) {
    return;
}

$config = new configuration_bot;
$data = json_decode(file_get_contents('php://input'));

if(strcmp($data->secret, $server_key) !== 0 && strcmp($data->type, 'confirmation') !== 0)
    return;

switch ($data->type) {
    case 'confirmation':
        echo $confirmation;
        break;

    case 'message_new':
        $userId = $data->object->user_id;
        $userInfo = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$userId}&v=5.0"));
        $user_name = $userInfo->response[0]->first_name;
        $request_params = array(
            'message' => "{$user_name}, ваше сообщение успешно принято!<br>".
                            "Мы постараемся ответить в ближайшее время.",
            'user_id' => $userId,
            'access_token' => $config->token,
            'v' => '5.0'
        );

        $get_params = http_build_query($request_params);

        file_get_contents('https://api.vk.com/method/messages.send?' . $get_params);
        echo('ok');

        break;
       case 'group_join':
       $userId = $data->object->user_id;
       $userInfo = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$userId}&v=5.0"));
       $user_name = $userInfo->response[0]->first_name;
       $request_params = array(
            'message' => "{$user_name}, мы рады приветствовать Вас в нашем сообществе!<br>".
                            "Следите за новостями и не упускайте важные моменты",
            'user_id' => $userId,
            'access_token' => $config->token,
            'v' => '5.0'
        );
        $get_params = http_build_query($request_params);

        file_get_contents('https://api.vk.com/method/messages.send?' . $get_params);
        echo('ok');
         break;
}
$logging = fopen("bot.log", "a++");
fputs(json_encode($data), $logging);
fclose($logging);
?>