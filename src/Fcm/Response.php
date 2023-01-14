<?php /** @noinspection ALL */

/**
 * Zend Framework (http://framework.zend.com/).
 *
 * @link       http://github.com/zendframework/zf2 for the canonical source repository
 *
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 *
 * @category   ZendService
 */

namespace ZendService\Google\Fcm;

use ZendService\Google\Exception\InvalidArgumentException;

/**
 * Google Cloud Messaging Response
 * This class parses out the response from
 * the Google Cloud Messaging API.
 *
 * @category   ZendService
 */
class Response
{
    /**
     * @const Message ID field
     */
    const RESULT_MESSAGE_ID = 'message_id';

    /**
     * @const Error field
     */
    const RESULT_ERROR = 'error';

    /**
     * @const Canonical field
     */
    const RESULT_CANONICAL = 'registration_id';

    /**
     * Error field responses
     * @link https://developers.google.com/cloud-messaging/http-server-ref#error-codes
     * @var string
     */
    const ERROR_MISSING_REGISTRATION = 'MissingRegistration';
    const ERROR_INVALID_REGISTRATION = 'InvalidRegistration';
    const ERROR_NOT_REGISTERED = 'NotRegistered';
    const ERROR_INVALID_PACKAGE_NAME = 'InvalidPackageName';
    const ERROR_MISMATCH_SENDER_ID = 'MismatchSenderId';
    const ERROR_MESSAGE_TOO_BIG = 'MessageTooBig';
    const ERROR_INVALID_DATA_KEY = 'InvalidDataKey';
    const ERROR_INVALID_TTL = 'InvalidTtl';
    const ERROR_UNAVAILABLE = 'Unavailable';
    const ERROR_INTERNAL_SERVER_ERROR = 'InternalServerError';
    const ERROR_DEVICE_MESSAGE_RATE_EXCEEDED = 'DeviceMessageRateExceeded';
    const ERROR_TOPICS_MESSAGE_RATE_EXCEEDED = 'TopicsMessageRateExceeded';

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $cntSuccess;

    /**
     * @var int
     */
    protected $cntFailure;

    /**
     * @var int
     */
    protected $cntCanonical;

    /**
     * @var ?Message
     */
    protected $message = null;

    /**
     * @var array
     */
    protected $results;

    /**
     * @var ?array
     */
    protected $response = null;

    /**
     * Constructor.
     *
     * @param array|null $response
     * @param ?Message $message
     *
     */
    public function __construct(?array $response = null, Message $message = null)
    {
        if ($response) {
            $this->setResponse($response);
        }

        if ($message) {
            $this->setMessage($message);
        }

        return $this;
    }

    /**
     * Get Message.
     *
     * @return ?Message
     */
    public function getMessage(): ?Message
    {
        return $this->message;
    }

    /**
     * Set Message.
     *
     * @param Message $message
     *
     * @return Response
     */
    public function setMessage(Message $message): Response
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get Response.
     *
     * @return array
     */
    public function getResponse(): ?array
    {
        return $this->response;
    }

    /**
     * Set Response.
     *
     * @param array $response
     *
     * @return Response
     *
     * @throws InvalidArgumentException
     */
    public function setResponse(array $response): Response
    {
        if (!isset(
            $response['results'],
            $response['success'],
            $response['failure'],
            $response['canonical_ids'],
            $response['multicast_id']
        )) {
            throw new InvalidArgumentException('Response did not contain the proper fields');
        }

        $this->response = $response;
        $this->results = $response['results'];
        $this->cntSuccess = (int)$response['success'];
        $this->cntFailure = (int)$response['failure'];
        $this->cntCanonical = (int)$response['canonical_ids'];
        $this->id = (int)$response['multicast_id'];

        return $this;
    }

    /**
     * Get Success Count.
     *
     * @return int
     */
    public function getSuccessCount(): int
    {
        return $this->cntSuccess;
    }

    /**
     * Get Failure Count.
     *
     * @return int
     */
    public function getFailureCount(): int
    {
        return $this->cntFailure;
    }

    /**
     * Get Canonical Count.
     *
     * @return int
     */
    public function getCanonicalCount(): int
    {
        return $this->cntCanonical;
    }

    /**
     * Get Results.
     *
     * @return array multi dimensional array of:
     *               NOTE: key is registration_id if the message is passed.
     *               'registration_id' => [
     *               'message_id' => 'id',
     *               'error' => 'error',
     *               'registration_id' => 'id'
     *               ]
     */
    public function getResults(): array
    {
        return $this->correlate();
    }

    /**
     * Get Singular Result.
     *
     * @param string $flag one of the RESULT_* flags
     *
     * @return array singular array with keys being registration id
     *               value is the type of result
     */
    public function getResult(string $flag): array
    {
        $ret = [];
        foreach ($this->correlate() as $k => $v) {
            if (isset($v[$flag])) {
                $ret[$k] = $v[$flag];
            }
        }

        return $ret;
    }

    /**
     * Correlate Message and Result.
     *
     * @return array
     */
    protected function correlate(): array
    {
        $results = $this->results;
        if ($this->message && $results) {
            $ids = $this->message->getRegistrationIds();
            while ($id = array_shift($ids)) {
                $results[$id] = array_shift($results);
            }
        }

        return $results;
    }
}
