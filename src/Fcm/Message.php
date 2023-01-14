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

use Laminas\Json\Json;
use ZendService\Google\Exception;
use ZendService\Google\Exception\InvalidArgumentException;

/**
 * Google Cloud Messaging Message
 * This class defines a message to be sent
 * through the Google Cloud Messaging API.
 *
 * @category   ZendService
 */
class Message
{
    /**
     * @var array
     */
    protected array $registrationIds = [];

    /**
     * @var ?string
     */
    protected ?string $collapseKey = null;

    /**
     * @var string
     */
    protected string $priority = 'normal';

    /**
     * @var array
     */
    protected array $data = [];

    /**
     * @var array
     */
    protected array $notification = [];

    /**
     * @var bool
     */
    protected bool $delayWhileIdle = false;

    /**
     * @var int
     */
    protected int $timeToLive = 2419200;

    /**
     * @var ?string
     */
    protected ?string $restrictedPackageName = null;

    /**
     * @var bool
     */
    protected bool $dryRun = false;

    /**
     * Set Registration Ids.
     *
     * @param array $ids
     *
     * @return Message
     * @throws InvalidArgumentException
     *
     */
    public function setRegistrationIds(array $ids): Message
    {
        $this->clearRegistrationIds();
        foreach ($ids as $id) {
            $this->addRegistrationId($id);
        }

        return $this;
    }

    /**
     * Get Registration Ids.
     *
     * @return array
     */
    public function getRegistrationIds(): array
    {
        return $this->registrationIds;
    }

    /**
     * Add Registration Ids.
     *
     * @param string $id
     *
     * @return Message
     *
     * @throws InvalidArgumentException
     */
    public function addRegistrationId(string $id): Message
    {
        if (empty($id)) {
            throw new InvalidArgumentException('$id must be a non-empty string');
        }
        if (!in_array($id, $this->registrationIds)) {
            $this->registrationIds[] = $id;
        }

        return $this;
    }

    /**
     * Clear Registration Ids.
     *
     * @return Message
     */
    public function clearRegistrationIds(): Message
    {
        $this->registrationIds = [];

        return $this;
    }

    /**
     * Get Collapse Key.
     *
     * @return string|null
     */
    public function getCollapseKey(): ?string
    {
        return $this->collapseKey;
    }

    /**
     * Set Collapse Key.
     *
     * @param ?string $key
     *
     * @return Message
     *
     * @throws InvalidArgumentException
     */
    public function setCollapseKey(?string $key): Message
    {
        if (null !== $key && !(strlen($key) > 0)) {
            throw new InvalidArgumentException('$key must be null or a non-empty string');
        }
        $this->collapseKey = $key;

        return $this;
    }

    /**
     * Get priority
     *
     * @return string
     * @noinspection PhpUnused
     */
    public function getPriority(): string
    {
        return $this->priority;
    }

    /**
     * Set priority
     *
     * @param ?string $priority
     * @return Message
     * @throws InvalidArgumentException
     * @noinspection PhpUnused
     */
    public function setPriority(?string $priority): static
    {
        if (!is_null($priority) && !(strlen($priority) > 0)) {
            throw new InvalidArgumentException('$priority must be null or a non-empty string');
        }
        $this->priority = $priority;
        return $this;
    }

    /**
     * Set Data
     *
     * @param array $data
     *
     * @return Message
     * @throws InvalidArgumentException
     *
     */
    public function setData(array $data): static
    {
        $this->clearData();
        foreach ($data as $k => $v) {
            $this->addData($k, $v);
        }

        return $this;
    }

    /**
     * Get Data.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Add Data.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return Message
     * @throws InvalidArgumentException
     *
     * @throws Exception\RuntimeException
     */
    public function addData(string $key, mixed $value): static
    {
        if (empty($key)) {
            throw new InvalidArgumentException('$key must be a non-empty string');
        }
        if (array_key_exists($key, $this->data)) {
            throw new Exception\RuntimeException('$key conflicts with current set data');
        }
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Clear Data.
     *
     * @return Message
     */
    public function clearData(): Message
    {
        $this->data = [];

        return $this;
    }

    /**
     * Set notification
     *
     * @param array $data
     * @return Message
     */
    public function setNotification(array $data): Message
    {
        $this->clearNotification();
        foreach ($data as $k => $v) {
            $this->addNotification($k, $v);
        }
        return $this;
    }

    /**
     * Get notification
     *
     * @return array
     */
    public function getNotification(): array
    {
        return $this->notification;
    }

    /**
     * Add notification data
     *
     * @param string $key
     * @param mixed $value
     * @return Message
     * @throws InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    public function addNotification(string $key, mixed $value): Message
    {
        if (empty($key)) {
            throw new InvalidArgumentException('$key must be a non-empty string');
        }
        if (array_key_exists($key, $this->notification)) {
            throw new Exception\RuntimeException('$key conflicts with current set data');
        }
        $this->notification[$key] = $value;
        return $this;
    }

    /**
     * Clear notification
     *
     * @return Message
     */
    public function clearNotification(): Message
    {
        $this->notification = [];

        return $this;
    }

    /**
     * Set Delay While Idle
     *
     * @param bool $delay
     *
     * @return Message
     */
    public function setDelayWhileIdle(bool $delay): Message
    {
        $this->delayWhileIdle = $delay;

        return $this;
    }

    /**
     * Get Delay While Idle.
     *
     * @return bool
     */
    public function getDelayWhileIdle(): bool
    {
        return $this->delayWhileIdle;
    }

    /**
     * Set Time to Live.
     *
     * @param int $ttl
     *
     * @return Message
     */
    public function setTimeToLive(int $ttl): Message
    {
        $this->timeToLive = $ttl;

        return $this;
    }

    /**
     * Get Time to Live.
     *
     * @return int
     */
    public function getTimeToLive(): int
    {
        return $this->timeToLive;
    }

    /**
     * Set Restricted Package Name.
     *
     * @param ?string $name
     *
     * @return Message
     *
     * @throws InvalidArgumentException
     */
    public function setRestrictedPackageName(?string $name): Message
    {
        if (null !== $name && !(strlen($name) > 0)) {
            throw new InvalidArgumentException('$name must be null OR a non-empty string');
        }
        $this->restrictedPackageName = $name;

        return $this;
    }

    /**
     * Get Restricted Package Name.
     *
     * @return string|null
     */
    public function getRestrictedPackageName(): ?string
    {
        return $this->restrictedPackageName;
    }

    /**
     * Set Dry Run.
     *
     * @param bool $dryRun
     *
     * @return Message
     */
    public function setDryRun(bool $dryRun): Message
    {
        $this->dryRun = $dryRun;

        return $this;
    }

    /**
     * Get Dry Run.
     *
     * @return bool
     */
    public function getDryRun(): bool
    {
        return $this->dryRun;
    }

    /**
     * To JSON
     * Utility method to put the JSON into the
     * GCM proper format for sending the message.
     *
     * @return string
     */
    public function toJson(): string
    {
        $json = [];
        if ($this->registrationIds) {
            $json['registration_ids'] = $this->registrationIds;
        }
        if ($this->collapseKey) {
            $json['collapse_key'] = $this->collapseKey;
        }
        if ($this->priority) {
            $json['priority'] = $this->priority;
        }
        if ($this->data) {
            $json['data'] = $this->data;
        }
        if ($this->notification) {
            $json['notification'] = $this->notification;
        }
        if ($this->delayWhileIdle) {
            $json['delay_while_idle'] = $this->delayWhileIdle;
        }
        if ($this->timeToLive != 2419200) {
            $json['time_to_live'] = $this->timeToLive;
        }
        if ($this->restrictedPackageName) {
            $json['restricted_package_name'] = $this->restrictedPackageName;
        }
        if ($this->dryRun) {
            $json['dry_run'] = $this->dryRun;
        }

        return Json::encode($json);
    }
}
