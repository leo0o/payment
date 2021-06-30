<?php
require_once __DIR__ . '/../../vendor/autoload.php';


date_default_timezone_set('Asia/Shanghai');
$aliConfig = require_once __DIR__ . '/../aliconfig.php';

// 协议解约，沙箱环境不支持
$tradeNo = time() . rand(1000, 9999);
$payData = [
    'contract_user_id'         => '1',
    'contract_logon_id'      => '1',
    'contract_product_code'     => 'CYCLE_PAY_AUTH_P',
    'contract_sign_scene'  => '', // 签约时传入的签约场景
    'internal_contract_id'       => '', // 商户协议号
    'contract_id' => '', // 第三方协议号
    'extend_params' => '',
];

// 使用
try {
    $client = new \Payment\Client(\Payment\Client::ALIPAY, $aliConfig);
    $res    = $client->agreementUnsign($payData);
} catch (InvalidArgumentException $e) {
    echo $e->getMessage();
    exit;
} catch (\Payment\Exceptions\GatewayException $e) {
    echo $e->getMessage();
    var_dump($e->getRaw());
    exit;
} catch (\Payment\Exceptions\ClassNotFoundException $e) {
    echo $e->getMessage();
    exit;
} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}

echo $res;// 这里如果直接输出到页面，&not 会被转义，请注意