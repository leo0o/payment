<?php


namespace Payment\Contracts;


interface IAgreementProxy
{
    /**
     * 协议解约接口
     * @param array $requestParams
     * @return mixed
     */
    public function agreementUnsign(array $requestParams);
}