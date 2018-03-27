<?php

// By @NimaH79

function apiRequest($method, $parameters)
{
    foreach ($parameters as $key => &$val) {
        if (is_array($val)) {
            $val = json_encode($val);
        }
    }
    $handle = curl_init('https://api.telegram.org/bot<YOUR_BOT_TOKEN>/'.$method);
    curl_setopt($handle, CURLOPT_POSTFIELDS, $parameters);
    curl_exec($handle);
    curl_close($handle);
}
function glitch($image)
{
    $hex_img = file_get_contents($image);
    $str = bin2hex($hex_img);
    for ($i = 0; $i < 40; $i++) {
        $rand_n = rand(8, strlen($str));
        $remove_n = (8 * rand(1, 40));
        $max_length = (strlen($str) - 1000);
        if (is_int($rand_n / 8) && $rand_n > 1000 && ($rand_n + $remove_n) < $max_length) {
            $string_to_cut = substr($str, $rand_n, $remove_n);
            $str = str_replace($string_to_cut, '', $str);
        }
    }
    $str = hex2bin($str);
    if (imagecreatefromstring($str)) {
        $img = imagecreatefromstring($str);
    } else {
        $str = file_get_contents($_GET['src']);
        $img = imagecreatefromstring($str);
    }
    imagejpeg($img, 'glitched.jpg');
    imagedestroy($img);
}
function processMessage($message)
{
    $chat_id = $message['chat']['id'];
    if (isset($message['text'])) {
        apiRequest('sendMessage', ['chat_id' => $chat_id, 'text' => "Hello!\n\nPlease send your photo to glitch.\n\nBy @NimaH79"]);
    }
    if (isset($message['photo'])) {
        $file_path = json_decode(file_get_contents('http://api.telegram.org/bot318652867:AAEGlzU5gRher0XDw3bOR-A_Bkylhc0rxsg/getFile?file_id='.$message['photo'][count($message['photo']) - 1]['file_id']), true)['result']['file_path'];
        file_put_contents('toglitch.jpg', fopen('http://api.telegram.org/file/bot318652867:AAEGlzU5gRher0XDw3bOR-A_Bkylhc0rxsg/'.$file_path, 'r'));
        glitch('toglitch.jpg');
        unlink('toglitch.jpg');
        apiRequest('sendPhoto', ['chat_id' => $chat_id, 'photo' => new CURLFile('glitched.jpg')]);
        unlink('glitched.jpg');
    }
}
processMessage(json_decode(file_get_contents('php://input'), true)['message']);
