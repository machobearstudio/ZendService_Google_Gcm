<?php
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

namespace ZendServiceTest\Google\Gcm;

use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use TypeError;
use ZendService\Google\Exception\InvalidArgumentException;
use ZendService\Google\Fcm\Message;
use ZendService\Google\Fcm\Response;

/**
 * @category   ZendService
 * @group      ZendService
 * @group      ZendService_Google
 * @group      ZendService_Google_Gcm
 */
class ResponseTest extends TestCase
{
    /**
     * @var Message
     */
    protected Message $m;

    public function setUp(): void
    {
        $this->m = new Message();
    }

    public function testConstructorExpectedBehavior()
    {
        $response = new Response();
        self::assertNull($response->getResponse());
        self::assertNull($response->getMessage());

        $message = new Message();
        $response = new Response(null, $message);
        self::assertEquals($message, $response->getMessage());
        self::assertNull($response->getResponse());

        $message = new Message();
        $responseArray = [
            'results' => [
                ['message_id' => '1:1234'],
            ],
            'success' => 1,
            'failure' => 0,
            'canonical_ids' => 0,
            'multicast_id' => 1,
        ];
        $response = new Response($responseArray, $message);
        self::assertEquals($responseArray, $response->getResponse());
        self::assertEquals($message, $response->getMessage());
    }

    public function testInvalidConstructorThrowsException()
    {
        if (PHP_VERSION_ID < 70000) {
            self::markTestSkipped('PHP 7 required.');
        }

        $this->expectException(InvalidArgumentException::class);
        new Response(['{bad']);
    }

    public function testMessageExpectedBehavior()
    {
        $message = new Message();
        $response = new Response();
        $response->setMessage($message);
        self::assertEquals($message, $response->getMessage());
    }

    public function testResponse()
    {
        $responseArr = [
            'results' => [
                ['message_id' => '1:234'],
            ],
            'success' => 1,
            'failure' => 0,
            'canonical_ids' => 0,
            'multicast_id' => '123',
        ];
        $response = new Response();
        $response->setResponse($responseArr);
        self::assertEquals($responseArr, $response->getResponse());
        self::assertEquals(1, $response->getSuccessCount());
        self::assertEquals(0, $response->getFailureCount());
        self::assertEquals(0, $response->getCanonicalCount());
        // test results non correlated
        $expected = [['message_id' => '1:234']];
        self::assertEquals($expected, $response->getResults());
        $expected = [0 => '1:234'];
        self::assertEquals($expected, $response->getResult(Response::RESULT_MESSAGE_ID));

        $message = new Message();
        $message->setRegistrationIds(['ABCDEF']);
        $response->setMessage($message);
        $expected = ['ABCDEF' => '1:234'];
        self::assertEquals($expected, $response->getResult(Response::RESULT_MESSAGE_ID));
    }
}
