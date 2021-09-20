<?php

namespace Unleash\Client\DTO;

final class DefaultVariantOverride implements VariantOverride
{
    /**
     * @var string
     */
    private $field;
    /**
     * @var string[]
     */
    private $values;
    /**
     * @param array<string> $values
     */
    public function __construct(string $field, array $values)
    {
        $this->field = $field;
        $this->values = $values;
    }
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return array<string>
     */
    public function getValues(): array
    {
        return $this->values;
    }
}
