<?php
$config = array(
    'url' => 'https://help.ssangyongsports.eu.org/help/api/tickets.json',
    'key1' => 'CED4211CA152BBDA90831B1F719D2E3C',
    'key2' => 'AC1AEA4935E116E407EE6EEC1AA3D77E'  // 請替換為您的第二個 API key
);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = array(
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'subject' => $_POST['subject'],
        'message' => $_POST['message'],
        'ip' => $_SERVER['REMOTE_ADDR'],
        'attachments' => array(),
    );

    function_exists('curl_version') or die('CURL support required');
    function_exists('json_encode') or die('JSON support required');

    set_time_limit(30);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $config['url']);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_USERAGENT, 'osTicket API Client v1.7');
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Expect:', 
        'X-API-Key1: ' . $config['key1'],
        'X-API-Key2: ' . $config['key2']
    ));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    $result = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        die('cURL error: ' . curl_error($ch));
    }

    curl_close($ch);

    if ($code != 201) {
        die('Unable to create ticket: ' . $result);
    }

    $ticket_id = json_decode($result, true)['ticket_id'];
    header("Location: /thanks?id=" . urlencode($ticket_id));
    exit();
}
?>
