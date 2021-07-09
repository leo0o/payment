<?php


namespace Payment\Gateways\Alipay;


use Payment\Contracts\IGatewayRequest;
use Payment\Exceptions\GatewayException;
use Payment\Helpers\ArrayUtil;
use Payment\Payment;

/**
 * Class AgreementUnsign
 * @package Payment\Gateways\Alipay
 * 支付宝个人代扣协议解约接口
 * https://opendocs.alipay.com/apis/api_2/alipay.user.agreement.unsign
 */
class AgreementUnsign extends AliBaseObject implements IGatewayRequest
{
    const METHOD = 'alipay.user.agreement.unsign';

    protected function getBizContent(array $requestParams)
    {
        // alipay_user_id 与 alipay_logon_id 不能同时为空，若都传以 alipay_user_id 为准
        // agreement_no 与 external_agreement_no 不能同时为空，若都传以 agreement_no 为准
        $bizContent = [
            'alipay_user_id'  => $requestParams['contract_user_id'] ?? '',
            'alipay_logon_id' => $requestParams['contract_logon_id'] ?? '',
            'personal_product_code'  => $requestParams['contract_product_code'] ?? 'CYCLE_PAY_AUTH_P',
            'sign_scene'      => $requestParams['contract_sign_scene'] ?? '',
            'external_agreement_no'  => $requestParams['internal_contract_id'] ?? '',
            'agreement_no'    => $requestParams['contract_id'] ?? '',
            'extend_params'   => $requestParams['extend_params'] ?? [],
        ];
        $bizContent = ArrayUtil::paraFilter($bizContent);

        return $bizContent;
    }

    public function request(array $requestParams)
    {
        try {
            $params = $this->buildParams(self::METHOD, $requestParams);
            $ret    = $this->post($this->gatewayUrl, $params);
            $retArr = json_decode($ret, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new GatewayException(sprintf('format agreement unsign get error, [%s]', json_last_error_msg()), Payment::FORMAT_DATA_ERR, ['raw' => $ret]);
            }

            $content = $retArr['alipay_user_agreement_unsign_response'];
            if ($content['code'] !== self::REQ_SUC) {
                throw new GatewayException(sprintf('request get failed, msg[%s], sub_msg[%s]', $content['msg'], $content['sub_msg']), Payment::SIGN_ERR, $content);
            }

            $signFlag = $this->verifySign($content, $retArr['sign']);
            if (!$signFlag) {
                throw new GatewayException('check sign failed', Payment::SIGN_ERR, $retArr);
            }

            return $content;
        } catch (GatewayException $e) {
            throw $e;
        }
    }
}