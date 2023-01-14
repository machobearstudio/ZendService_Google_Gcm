<?php
/**
 * Zend Framework (http://framework.zend.com/).
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 *
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 *
 * @category  ZendService
 */

namespace ZendService\Google\Fcm;

use InvalidArgumentException;
use Laminas\Http\Client as HttpClient;
use Laminas\Json\Json;
use ZendService\Google\Exception;
use ZendService\Google\Exception\RuntimeException;

/**
 * Firebase Cloud Messaging Client
 * This class allows the ability to send out messages
 * through the Firebase Cloud Messaging API.
 *
 * @category   ZendService
 */
class Client
{
    /**
     * @const string Server URI
     */
    const SERVER_URI = 'https://fcm.googleapis.com/fcm/send';

    /**
     * @var ?HttpClient
     */
    protected ?HttpClient $httpClient = null;

    /**
     * @var string
     */
    protected string $apiKey;

    /**
     * Get API Key.
     *
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Set API Key.
     *
     * @param string $apiKey
     *
     * @return Client
     *
     * @noinspection PhpUnused
     */
    public function setApiKey(string $apiKey): Client
    {
        if (empty($apiKey)) {
            throw new Exception\InvalidArgumentException('The api key must be a string and not empty');
        }
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Get HTTP Client.
     *
     * @return HttpClient
     * @throws InvalidArgumentException
     *
     */
    public function getHttpClient(): HttpClient
    {
        if (!$this->httpClient) {
            $this->httpClient = new HttpClient();
            $this->httpClient->setOptions(['strictredirects' => true]);
        }

        return $this->httpClient;
    }

    /**
     * Set HTTP Client.
     *
     * @param HttpClient $http
     *
     * @return Client
     * @noinspection PhpUnused
     */
    public function setHttpClient(HttpClient $http): Client
    {
        $this->httpClient = $http;

        return $this;
    }

    /**
     * Send Message.
     *
     * @param Message $message
     *
     * @return Response
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws Exception\InvalidArgumentException
     *
     * @throws RuntimeException
     */
    public function send(Message $message): Response
    {
        $client = $this->getHttpClient();
        $client->setUri(self::SERVER_URI);
        $headers = $client->getRequest()->getHeaders();
        $headers->addHeaderLine('Authorization', 'key=' . $this->getApiKey());
        $headers->addHeaderLine('Content-length', mb_strlen($message->toJson()));

        $response = $client->setHeaders($headers)
            ->setMethod('POST')
            ->setRawBody($message->toJson())
            ->setEncType('application/json')
            ->send();

        switch ($response->getStatusCode()) {
            case 500:
                throw new RuntimeException('500 Internal Server Error');
            case 503:
                $exceptionMessage = '503 Server Unavailable';
                if ($retry = $response->getHeaders()->get('Retry-After')) {
                    $exceptionMessage .= '; Retry After: ' . $retry;
                }
                throw new RuntimeException($exceptionMessage);
            case 401:
                throw new RuntimeException('401 Forbidden; Authentication Error');
            case 400:
                throw new RuntimeException('400 Bad Request; invalid message');
        }

        if (!$response = Json::decode($response->getBody(), Json::TYPE_ARRAY)) {
            throw new RuntimeException('Response body did not contain a valid JSON response');
        }

        return new Response($response, $message);
    }
}
