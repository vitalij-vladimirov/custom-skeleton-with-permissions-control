<?php

declare(strict_types=1);

namespace Core\Entity\Database;

use Core\Service\Database\DB;
use Core\Service\DependencyInjector;
use Core\Service\Database\QueryBuilder;
use Core\Service\UuidGenerator;
use DateTimeImmutable;
use ReflectionClass;
use ReflectionProperty;

abstract class BaseDbEntity
{
    private self $original;

    protected const DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * @var FieldProperty[]
     */
    private array $fieldsProperties;
    private DB $db;

    protected array $restrictModify = [
        'id',
        'createdAt',
        'updatedAt',
    ];

    protected array $autogenerate = [
        'uuid'
    ];

    protected array $hidden = [
        'id',
        'password',
        'createdAt',
        'updatedAt',
    ];

    final public function __construct()
    {
        $this->db = new DB();
    }

    abstract public function getTable(): string;

    public static function query(): QueryBuilder
    {
        return new QueryBuilder(new static);
    }

    public static function rawQuery(string $query): array
    {
        return (new static())->db->rawQuery($query);
    }

    public function save(): ?self
    {
        if (!isset($this->original, $this->original->id) || $this->original->id === null) {
            $this->autogenerateFields();
            $data = $this->generateInsertData();

            $id = $this->db->insert($this->getTable(), $data);

            // TODO: Throw exception here
            if ($id === null) {
                return null;
            }

            return $this::query()
                ->whereQuery(sprintf('id = \'%s\'', $id))
                ->first();
        }

        $data = $this->generateUpdateData();
        if (count($data) === 0) {
            return $this;
        }

        $this->db->update($this->getTable(), $this->original->id, $data);

        return $this;
    }

    public function delete(): bool
    {
        return $this->db->delete($this->getTable(), $this->id);
    }

    public function getFields(): array
    {
        $fieldsProperties = $this->getDatabaseFieldsProperties();

        $fields = [];
        foreach ($fieldsProperties as $fieldProperty) {
            $fields[] = $fieldProperty->snakeCaseName;
        }

        return $fields;
    }

    public function toArray(self $entity = null): array
    {
        $entity ??= $this;

        $normalizedEntity = [];

        $fields = $this->getDatabaseFieldsProperties();

        foreach ($fields as $field) {
            if (in_array($field->camelCaseName, $entity->hidden, true)) {
                continue;
            }

            if (!isset($entity->{$field->camelCaseName})) {
                $normalizedEntity[$field->snakeCaseName] = null;

                continue;
            }

            if ($entity->isDateTime($field)) {
                /** @var DateTimeImmutable|null $fieldValue */
                $fieldValue = $entity->{$field->camelCaseName};

                $normalizedEntity[$field->snakeCaseName] = $fieldValue !== null
                    ? $fieldValue->format(self::DATETIME_FORMAT)
                    : null;

                continue;
            }

            $normalizedEntity[$field->snakeCaseName] = $entity->{$field->camelCaseName};
        }

        return $normalizedEntity;
    }

    /**
     * @param self[] $entities
     *
     * @return array
     */
    public static function multipleToArray(array $entities): array
    {
        $instance = new static();

        $normalizerEntityList = [];

        foreach ($entities as $entity) {
            $normalizerEntityList[] = $instance->toArray($entity);
        }

        return $normalizerEntityList;
    }

    public function toEntity(array $row): self
    {
        $entity = new static();

        $fields = $entity->getDatabaseFieldsProperties();
        foreach ($fields as $field) {
            if (!isset($row[$field->snakeCaseName])) {
                continue;
            }

            $value = $row[$field->snakeCaseName];

            switch ($field->type) {
                case 'string':
                    $entity->{$field->camelCaseName} = $value !== null ? (string) $value : null;

                    continue 2;
                case 'int':
                    $entity->{$field->camelCaseName} = $value !== null ? (int) $value : null;

                    continue 2;
                case 'float':
                    $entity->{$field->camelCaseName} = $value !== null ? (float) $value : null;

                    continue 2;
                case 'bool':
                    $entity->{$field->camelCaseName} = $value !== null ? (bool) $value : null;

                    continue 2;
                default:
                    if ($entity->isDateTime($field)) {
                        $entity->{$field->camelCaseName} = $value !== null
                            ? DateTimeImmutable::createFromFormat(self::DATETIME_FORMAT, $value)
                            : null;

                        continue 2;
                    }

                    $entity->{$field->camelCaseName} = $value ?? null;
            }
        }

        $entity->original = clone $entity;

        return $entity;
    }

    protected function generateUuid(): string
    {
        /** @var UuidGenerator $uuidGenerator */
        $uuidGenerator = (new DependencyInjector())->inject(UuidGenerator::class);

        return $uuidGenerator->generate($this);
    }

    /**
     * @return FieldProperty[]
     */
    private function getDatabaseFieldsProperties(): array
    {
        if (isset($this->fieldsProperties)) {
            return $this->fieldsProperties;
        }

        $entity = new static();
        $entityReflection = new ReflectionClass($entity);

        foreach ($entityReflection->getProperties() as $property) {
            if ($property->class !== static::class || !$property->isPublic()) {
                continue;
            }

            $this->fieldsProperties[] = $this->getDatabaseFieldProperties($property);
        }

        return $this->fieldsProperties;
    }

    private function getDatabaseFieldProperties(ReflectionProperty $property): FieldProperty
    {
        $field = new FieldProperty();
        $propertyType = $property->getType();
        $field->type = $propertyType !== null ? $propertyType->getName() : 'string';
        $field->isNullable = $propertyType !== null ? $propertyType->allowsNull() : false;
        $field->camelCaseName = $property->getName();

        $camelToSnake = ltrim(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $field->camelCaseName), '_');
        $field->snakeCaseName = strtolower($camelToSnake);

        return $field;
    }

    private function isDateTime(FieldProperty $field): bool
    {
        return strpos($field->type, 'DateTime') !== false;
    }

    private function autogenerateFields(): void
    {
        $fields = $this->getDatabaseFieldsProperties();
        foreach ($fields as $field) {
            if (!in_array($field->camelCaseName, $this->autogenerate, true)) {
                continue;
            }

            $generateMethod = sprintf('generate%s', ucfirst($field->camelCaseName));
            if (!method_exists(static::class, $generateMethod)) {
                // TODO: throw exception here
                continue;
            }

            $this->{$field->camelCaseName} = $this->$generateMethod();
        }
    }

    private function generateInsertData(): array
    {
        $data = [];

        $fields = $this->getDatabaseFieldsProperties();
        foreach ($fields as $field) {
            if (
                !isset($this->{$field->camelCaseName})
                || in_array($field->camelCaseName, $this->restrictModify, true)
            ) {
                continue;
            }

            if ($field->type === 'bool') {
                $data[$field->snakeCaseName] = (int) $this->{$field->camelCaseName};

                continue;
            }

            $data[$field->snakeCaseName] = $this->{$field->camelCaseName};
        }

        return $data;
    }

    private function generateUpdateData(): array
    {
        $data = [];

        $fields = $this->getDatabaseFieldsProperties();
        foreach ($fields as $field) {
            // Do not update unchanged, autogenerated and restricted to update fields
            if (
                $this->{$field->camelCaseName} === $this->original->{$field->camelCaseName}
                || in_array($field->camelCaseName, $this->autogenerate, true)
                || in_array($field->camelCaseName, $this->restrictModify, true)
            ) {
                continue;
            }

            $data[$field->snakeCaseName] = $this->{$field->camelCaseName};
        }

        return $data;
    }
}
