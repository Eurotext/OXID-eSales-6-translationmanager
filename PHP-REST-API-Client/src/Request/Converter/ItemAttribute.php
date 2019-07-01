<?php
declare(strict_types=1);

namespace Eurotext\RestApiClient\Request\Converter;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class ItemAttribute implements NameConverterInterface
{
    /**
     * @var string[]
     */
    private $mapping = [
        'meta'           => '__meta',
        'originalString' => 'original_string',
    ];

    /**
     * Converts a property name to its normalized value.
     *
     * @param string $propertyName
     *
     * @return string
     */
    public function normalize($propertyName): string
    {
        return $this->mapping[$propertyName] ?? $propertyName;
    }

    /**
     * Converts a property name to its denormalized value.
     *
     * @param string $propertyName
     *
     * @return string
     */
    public function denormalize($propertyName): string
    {
        return array_flip($this->mapping)[$propertyName] ?? $propertyName;
    }
}
