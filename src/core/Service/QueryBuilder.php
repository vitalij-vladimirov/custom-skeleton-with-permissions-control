<?php

declare(strict_types=1);

namespace Core\Service;

use Core\Entity\DbEntity;

class QueryBuilder
{
    private DbEntity $entity;
    private DB $db;
    private array $select;
    private ?string $where = null;
    private array $groupBy = [];
    private ?string $orderBy = null;
    private ?string $orderDirection = null;
    private ?string $having = null;
    private ?int $limit = null;
    private ?int $offset = null;

    public function __construct(DbEntity $entity)
    {
        $this->setup($entity);
    }

    public function select(... $fields): self
    {
        $entityFields = $this->entity->getFields();

        $this->select = [];
        foreach ($fields as $field) {
            if (!in_array($field, $entityFields, true)) {
                continue;
            }

            $this->select[] = $field;
        }

        if (count($this->select) === 0) {
            $this->select = $entityFields;
        }

        return $this;
    }

    public function whereQuery(string $query): self
    {
        $this->where = str_replace(';', '', $query);

        return $this;
    }

    public function groupBy(... $fields): self
    {
        $entityFields = $this->entity->getFields();

        $this->groupBy = [];
        foreach ($fields as $field) {
            if (!in_array($field, $entityFields, true)) {
                continue;
            }

            $this->groupBy[] = $field;
        }

        return $this;
    }

    public function orderBy(string $field, string $direction = 'asc'): self
    {
        if (!in_array($field, $this->select, true)) {
            return $this;
        }

        $this->orderBy = $field;
        $this->orderDirection = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';

        return $this;
    }

    public function havingQuery(string $query): self
    {
        $this->having = str_replace(';', '', $query);

        return $this;
    }

    public function offset(int $offset): self
    {
        if ($offset < 0) {
            return $this;
        }

        $this->offset = $offset;

        return $this;
    }

    public function limit(int $limit): self
    {
        if ($limit < 1) {
            return $this;
        }

        $this->limit = $limit;

        return $this;
    }

    public function getRawQuery(): string
    {
        return $this->buildQuery();
    }

    public function count(): int
    {
        return $this->db->count($this->buildQuery());
    }

    public function exists(): bool
    {
        return $this->count() > 0;
    }

    /**
     * @return DbEntity[]
     */
    public function get(): array
    {
        $query = $this->buildQuery();
        $result = $this->db->rawQuery($query);

        if (count($result) === 0) {
            return  [];
        }

        $entity = [];
        foreach ($result as $row) {
            $entity[] = $this->entity->toEntity($row);
        }

        return $entity;
    }

    public function first(): ?DbEntity
    {
        $this->limit(1);
        $entity = $this->get();

        if (count($entity) === 0) {
            return null;
        }

        return $entity[0];
    }

    private function setup(DbEntity $entity): void
    {
        $entityFields = $entity->getFields();

        $this->entity = $entity;
        $this->db = new DB();
        $this->select = $entityFields;
    }

    private function buildQuery(): string
    {
        $query = sprintf(
            'SELECT %s FROM %s ',
            implode(', ', $this->select),
            $this->entity->getTable()
        );

        if ($this->where !== null) {
            $query .= sprintf('WHERE %s ', $this->where);
        }

        if (count($this->groupBy) !== 0) {
            $query .= sprintf('GROUP BY %s ', implode(', ', $this->groupBy));
        }

        if ($this->orderBy !== null) {
            $query .= sprintf('ORDER BY %s %s ', $this->orderBy, $this->orderDirection ?? 'ASC');
        }

        if ($this->having !== null) {
            $query .= sprintf('HAVING %s ', $this->having);
        }

        if ($this->limit !== null && $this->limit >= 1) {
            $query .= sprintf('LIMIT %d ', $this->limit);
        }

        if ($this->offset !== null && $this->offset >= 1) {
            $query .= sprintf('OFFSET %d ', $this->offset);
        }

        return $query;
    }
}
