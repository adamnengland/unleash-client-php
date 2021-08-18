<?php

namespace Unleash\Client\Configuration;

use Unleash\Client\Enum\ContextField;
use Unleash\Client\Enum\Stickiness;
use Unleash\Client\Exception\InvalidValueException;

final class UnleashContext implements Context
{
    /**
     * @var string|null
     */
    private $currentUserId;
    /**
     * @var string|null
     */
    private $ipAddress;
    /**
     * @var string|null
     */
    private $sessionId;
    /**
     * @var mixed[]
     */
    private $customContext = [];
    /**
     * @param array<string,string> $customContext
     */
    public function __construct(?string $currentUserId = null, ?string $ipAddress = null, ?string $sessionId = null, array $customContext = [])
    {
        $this->currentUserId = $currentUserId;
        $this->ipAddress = $ipAddress;
        $this->sessionId = $sessionId;
        $this->customContext = $customContext;
    }
    public function getCurrentUserId(): ?string
    {
        return $this->currentUserId;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress ?? $_SERVER['REMOTE_ADDR'] ?? null;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId ?? (session_id() ?: null);
    }

    public function getCustomProperty(string $name): string
    {
        if (!array_key_exists($name, $this->customContext)) {
            throw new InvalidValueException("The custom context value '{$name}' does not exist");
        }

        return $this->customContext[$name];
    }

    /**
     * @return $this
     */
    public function setCustomProperty(string $name, string $value)
    {
        $this->customContext[$name] = $value;

        return $this;
    }

    public function hasCustomProperty(string $name): bool
    {
        return array_key_exists($name, $this->customContext);
    }

    /**
     * @return $this
     */
    public function removeCustomProperty(string $name, bool $silent = true)
    {
        if (!$this->hasCustomProperty($name) && !$silent) {
            throw new InvalidValueException("The custom context value '{$name}' does not exist");
        }

        unset($this->customContext[$name]);

        return $this;
    }

    /**
     * @return $this
     */
    public function setCurrentUserId(?string $currentUserId)
    {
        $this->currentUserId = $currentUserId;

        return $this;
    }

    /**
     * @return $this
     */
    public function setIpAddress(?string $ipAddress)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * @return $this
     */
    public function setSessionId(?string $sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * @param array<string> $values
     */
    public function hasMatchingFieldValue(string $fieldName, array $values): bool
    {
        $fieldValue = $this->findContextValue($fieldName);
        if ($fieldValue === null) {
            return false;
        }

        return in_array($fieldValue, $values, true);
    }

    public function findContextValue(string $fieldName): ?string
    {
        switch ($fieldName) {
            case ContextField::USER_ID:
            case Stickiness::USER_ID:
                return $this->getCurrentUserId();
            case ContextField::SESSION_ID:
            case Stickiness::SESSION_ID:
                return $this->getSessionId();
            case ContextField::IP_ADDRESS:
                return $this->getIpAddress();
            default:
                return $this->customContext[$fieldName] ?? null;
        }
    }
}
