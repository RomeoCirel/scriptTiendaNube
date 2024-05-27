<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/catch-data') {
    $headers = ['id', 'data', 'ip', 'active_vpn', 'forwarded_for', 'user_agent', 'referer', 'request_uri', 'browser', 'is_incognito', 'is_mobile', 'Operating System', 'headers'];
    $file = 'log.csv';

    if (!file_exists($file) || filesize($file) == 0) {
        $header_line = implode(";", $headers);
        file_put_contents($file, $header_line . PHP_EOL);
    }

    $requestData = json_decode(file_get_contents('php://input'), true);
    $browser = get_browser($_SERVER['HTTP_USER_AGENT'], true) ?? null;

    $TO_LOG = [];
    $TO_LOG['id'] = $requestData['id'] ?? uniqid();
    $TO_LOG['data'] = json_encode($requestData['LS'] ?? null);
    $TO_LOG['ip'] = $_SERVER['REMOTE_ADDR'];
    $TO_LOG['active_vpn'] = checkVPN();
    $TO_LOG['forwarded_for'] = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null;
    $TO_LOG['user_agent'] = json_encode($_SERVER['HTTP_USER_AGENT']);
    $TO_LOG['referer'] = $_SERVER['HTTP_REFERER'];
    $TO_LOG['request_uri'] = $_SERVER['REQUEST_URI'];
    $TO_LOG['BROWSER'] = json_encode($browser);
    $TO_LOG['is_incognito'] = $requestData['isIncognito'] ? "si" : "no";
    $TO_LOG['is_mobile'] = $browser['ismobiledevice'] ?? null;
    $TO_LOG['Operating System'] = $browser['platform'] ?? null;
    $TO_LOG['headers'] = json_encode(getallheaders());

    $log_line = implode(";", $TO_LOG);
    file_put_contents('log.csv', $log_line . PHP_EOL, FILE_APPEND);

    http_response_code(200);
    echo json_encode(['status' => http_response_code(), 'id' => $TO_LOG['id']]);
}

function checkVPN(): string
{
    $vpnApiUrl = 'https://ipqualityscore.com/api/json/ip/29Ly8F9kGxcA3Y65nEyUk8pCQB7EhXex/';
    $ip = $_SERVER['REMOTE_ADDR'] || $_SERVER['HTTP_X_FORWARDED_FOR'][0] || null;

//    $ip = "2001:67c:2628:647:8::60"; // VPN IP
//    $ip = "45.230.45.233"; // Real IP

    if (!$ip) {
        return "no";
    }

    $url = $vpnApiUrl . $ip;
    $response = file_get_contents($url);
    $response = json_decode($response, true);
    return $response['active_vpn'] ? "si" : "no";
}