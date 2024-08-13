<?php

namespace ForumPay\PaymentGateway\Exception;

use ForumPay\PaymentGateway\PHPClient\Http\Exception\ApiExceptionInterface;

/**
 * ForumPay plugin exception
 */
class ApiHttpException extends ForumPayHttpException
{
    /**
     * ApiHttpException constructor
     *
     * @param ApiExceptionInterface|null $cause
     * @param int $code
     * @param int $httpCode
     */
    public function __construct(
        ApiExceptionInterface $cause = null,
        $code = 0,
        $httpCode = \Magento\Framework\Webapi\Exception::HTTP_BAD_REQUEST
    ) {
        $pos = strrpos(get_class($cause), '\\');
        $message = sprintf(
            "[%s] %s",
            $pos === false ? get_class($cause) : substr(get_class($cause), $pos + 1),
            $cause->getMessage()
        );

        parent::__construct(__($message), $code, $httpCode);
    }
}
