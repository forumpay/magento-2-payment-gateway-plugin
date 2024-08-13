<?php

namespace ForumPay\PaymentGateway\Exception;

class ForumPayHttpException extends \Magento\Framework\Webapi\Exception
{
    public const HTTP_BAD_REQUEST = 400;

    public const HTTP_FORBIDDEN = 403;

    public const HTTP_NOT_FOUND = 404;

    public const HTTP_INTERNAL_ERROR = 500;

    /**
     * Optional exception details.
     *
     * @var array
     */
    protected $details;

    /**
     * HTTP status code associated with current exception.
     *
     * @var int
     */
    protected $httpCode;

    /**
     * Exception name
     *
     * @var string
     */
    protected $name;

    /**
     * Exception CF Ray id
     *
     * @var string
     */
    protected $cfRayId;

    /**
     * Exception Stacktrace
     *
     * @var string
     */
    protected $stackTrace;

    /**
     * List of errors
     *
     * @var null|\Exception[]
     */
    protected $errors;

    /**
     * Initialize exception with HTTP code.
     *
     * @param string $message
     * @param string $cfRayId
     * @param int $code Error code
     * @param int $httpCode
     * @param array $details Additional exception details
     * @param string $name Exception name
     * @param \Exception[]|null $errors Array of errors messages
     * @param string $stackTrace
     *
     * @throws \InvalidArgumewntException
     */
    public function __construct(
        string $message,
        string $cfRayId,
        $code = 0,
        $httpCode = self::HTTP_BAD_REQUEST,
        array $details = [],
        $name = '',
        $errors = null,
        $stackTrace = null
    ) {
        /** Only HTTP error codes are allowed. No success or redirect codes must be used. */
        if ($httpCode < 400 || $httpCode > 599) {
            throw new \InvalidArgumentException(sprintf('The specified HTTP code "%d" is invalid.', $httpCode));
        }
        parent::__construct(__($message), $code);
        $this->code = $code;
        $this->httpCode = $httpCode;
        $this->details = $details;
        $this->name = $name;
        $this->cfRayId = $cfRayId;
        $this->errors = $errors;
        $this->stackTrace = $stackTrace;
    }

    /**
     * Retrieve current HTTP code.
     *
     * @return int
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * Retrieve exception details.
     *
     * @return array
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Retrieve exception name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Retrieve request id.
     *
     * @return string
     */
    public function getCfRayId()
    {
        return $this->cfRayId;
    }

    /**
     * Retrieve list of errors.
     *
     * @return null|\Exception[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Retrieve stack trace string.
     *
     * @return null|string
     */
    public function getStackTrace()
    {
        return $this->stackTrace;
    }
}
