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

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use ZendService\Google\Fcm\Message;

/**
 * @category   ZendService
 * @group      ZendService
 * @group      ZendService_Google
 * @group      ZendService_Google_Gcm
 */
class MessageTest extends TestCase
{
    protected array $validRegistrationIds = [
        '1234567890',
        '0987654321',
    ];

    protected array $validData = [
        'key' => 'value',
        'key2' => [
            'value',
        ],
    ];

    /**
     * @var Message
     */
    private Message $m;

    public function setUp(): void
    {
        $this->m = new Message();
    }

    public function testExpectedRegistrationIdBehavior()
    {
        self::assertEquals([], $this->m->getRegistrationIds());
        self::assertStringNotContainsString('registration_ids', $this->m->toJson());
        $this->m->setRegistrationIds($this->validRegistrationIds);
        self::assertEquals($this->m->getRegistrationIds(), $this->validRegistrationIds);
        foreach ($this->validRegistrationIds as $id) {
            $this->m->addRegistrationId($id);
        }
        self::assertEquals($this->m->getRegistrationIds(), $this->validRegistrationIds);
        self::assertStringContainsString('registration_ids', $this->m->toJson());
        $this->m->clearRegistrationIds();
        self::assertEquals([], $this->m->getRegistrationIds());
        self::assertStringNotContainsString('registration_ids', $this->m->toJson());
        $this->m->addRegistrationId('1029384756');
        self::assertEquals(['1029384756'], $this->m->getRegistrationIds());
        self::assertStringContainsString('registration_ids', $this->m->toJson());
    }

    public function testInvalidRegistrationIdThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->m->addRegistrationId('');
    }

    public function testExpectedCollapseKeyBehavior()
    {
        self::assertEquals(null, $this->m->getCollapseKey());
        self::assertStringNotContainsString('collapse_key', $this->m->toJson());
        $this->m->setCollapseKey('my collapse key');
        self::assertEquals('my collapse key', $this->m->getCollapseKey());
        self::assertStringContainsString('collapse_key', $this->m->toJson());
        $this->m->setCollapseKey(null);
        self::assertEquals(null, $this->m->getCollapseKey());
        self::assertStringNotContainsString('collapse_key', $this->m->toJson());
    }

    public function testInvalidCollapseKeyThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->m->setCollapseKey('');
    }

    public function testExpectedDataBehavior()
    {
        self::assertEquals([], $this->m->getData());
        self::assertStringNotContainsString('data', $this->m->toJson());
        $this->m->setData($this->validData);
        self::assertEquals($this->m->getData(), $this->validData);
        self::assertStringContainsString('data', $this->m->toJson());
        $this->m->clearData();
        self::assertEquals([], $this->m->getData());
        self::assertStringNotContainsString('data', $this->m->toJson());
        $this->m->addData('mykey', 'myvalue');
        self::assertEquals(['mykey' => 'myvalue'], $this->m->getData());
        self::assertStringContainsString('data', $this->m->toJson());
    }

    public function testExpectedNotificationBehavior()
    {
        $this->assertEquals([], $this->m->getNotification());
        $this->assertStringNotContainsString('notification', $this->m->toJson());
        $this->m->setNotification($this->validData);
        $this->assertEquals($this->m->getNotification(), $this->validData);
        $this->assertStringContainsString('notification', $this->m->toJson());
        $this->m->clearNotification();
        $this->assertEquals([], $this->m->getNotification());
        $this->assertStringNotContainsString('notification', $this->m->toJson());
        $this->m->addNotification('mykey', 'myvalue');
        $this->assertEquals(['mykey' => 'myvalue'], $this->m->getNotification());
        $this->assertStringContainsString('notification', $this->m->toJson());
    }

    public function testInvalidDataThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->m->addData('', 'value');
    }

    public function testDuplicateDataKeyThrowsException()
    {
        $this->expectException(RuntimeException::class);
        $this->m->setData($this->validData);
        $this->m->addData('key', 'value');
    }

    public function testExpectedDelayWhileIdleBehavior()
    {
        self::assertEquals(false, $this->m->getDelayWhileIdle());
        self::assertStringNotContainsString('delay_while_idle', $this->m->toJson());
        $this->m->setDelayWhileIdle(true);
        self::assertEquals(true, $this->m->getDelayWhileIdle());
        self::assertStringContainsString('delay_while_idle', $this->m->toJson());
        $this->m->setDelayWhileIdle(false);
        self::assertEquals(false, $this->m->getDelayWhileIdle());
        self::assertStringNotContainsString('delay_while_idle', $this->m->toJson());
    }

    public function testExpectedTimeToLiveBehavior()
    {
        self::assertEquals(2419200, $this->m->getTimeToLive());
        self::assertStringNotContainsString('time_to_live', $this->m->toJson());
        $this->m->setTimeToLive(12345);
        self::assertEquals(12345, $this->m->getTimeToLive());
        self::assertStringContainsString('time_to_live', $this->m->toJson());
        $this->m->setTimeToLive(2419200);
        self::assertEquals(2419200, $this->m->getTimeToLive());
        self::assertStringNotContainsString('time_to_live', $this->m->toJson());
    }

    public function testExpectedRestrictedPackageBehavior()
    {
        self::assertEquals(null, $this->m->getRestrictedPackageName());
        self::assertStringNotContainsString('restricted_package_name', $this->m->toJson());
        $this->m->setRestrictedPackageName('my.package.name');
        self::assertEquals('my.package.name', $this->m->getRestrictedPackageName());
        self::assertStringContainsString('restricted_package_name', $this->m->toJson());
        $this->m->setRestrictedPackageName(null);
        self::assertEquals(null, $this->m->getRestrictedPackageName());
        self::assertStringNotContainsString('restricted_package_name', $this->m->toJson());
    }

    public function testInvalidRestrictedPackageThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->m->setRestrictedPackageName('');
    }

    public function testExpectedDryRunBehavior()
    {
        self::assertEquals(false, $this->m->getDryRun());
        self::assertStringNotContainsString('dry_run', $this->m->toJson());
        $this->m->setDryRun(true);
        self::assertEquals(true, $this->m->getDryRun());
        self::assertStringContainsString('dry_run', $this->m->toJson());
        $this->m->setDryRun(false);
        self::assertEquals(false, $this->m->getDryRun());
        self::assertStringNotContainsString('dry_run', $this->m->toJson());
    }
}
