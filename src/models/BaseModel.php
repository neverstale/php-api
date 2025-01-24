<?php

namespace neverstale\api\models;

use Illuminate\Support\Collection;
use neverstale\api\exceptions\UnknownPropertyException;

/**
 * Neverstale API Base Model
 *
 * @author Neverstale
 * @package neverstale/api
 * @since 1.0.0
 * @see http://docs.neverstale.io/api/
 */
abstract class BaseModel
{
    /**
     * @param array<string,mixed>|Collection<string,mixed> $properties
     * @throws \Exception
     */
    public function __construct(array|Collection $properties = [], bool $strict = false)
    {
        foreach ($properties as $name => $value) {
            try {
                if (property_exists($this, $name)) {
                    $this->setProperty($name, $value);
                } else {
                    if ($strict) {
                        throw new UnknownPropertyException("Property $name does not exist on " . get_class($this));
                    }
                }
            } catch (\Exception $e) {
                if ($strict) {
                    throw new UnknownPropertyException("Property $name does not exist on " . get_class($this));
                }
            }
        }
    }

    public function setProperty(string $name, mixed $value): void
    {
        $propertyType = (new \ReflectionProperty($this, $name))->getType();
        if ($propertyType instanceof \ReflectionNamedType) {
            switch ($propertyType->getName()) {
                case \DateTime::class:
                    $this->$name = $value instanceof \DateTime || empty($value) ? $value : new \DateTime($value);
                    break;
                case 'bool':
                    $this->$name = (bool)$value;
                    break;
                case 'int':
                    $this->$name = (int)$value;
                    break;
                case 'float':
                    $this->$name = (float)$value;
                    break;
                default:
                    if ($propertyType->isBuiltin()) {
                        $this->$name = $value;
                    } else {
                        $enumClass = $propertyType->getName();
                        if (enum_exists($enumClass) && is_subclass_of($enumClass, \BackedEnum::class)) {
                            /** @var class-string<\BackedEnum> $enumClass */
                            $this->$name = $enumClass::tryFrom($value);
                        } else {
                            $this->$name = $value;
                        }
                    }
                    break;
            }
        } else {
            $this->$name = $value;
        }
    }
    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        // @phpstan-ignore-next-line
        return json_decode(json_encode($this), true);
    }
}
