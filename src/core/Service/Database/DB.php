<?php

declare(strict_types=1);

namespace Core\Service\Database;

use PDO;
use Throwable;

class DB
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = $GLOBALS['pdo'];
    }

    public function rawQuery(string $query): array
    {
        $query = str_replace(';', '', $query);
        $result = $this->pdo->query($query);

        if ($result->rowCount() === 0) {
            return  [];
        }

        $rows = [];
        foreach ($result->fetchAll() as $row) {
            $rows[] = (array) $row;
        }

        return $rows;
    }

    public function count(string $query): int
    {
        $query = str_replace(';', '', $query);
        $result = $this->pdo->query($query);

        return $result->rowCount();
    }

    public function exists(string $query): bool
    {
        return $this->count($query) > 0;
    }

    public function insert(string $table, array $data): int
    {
        $query = sprintf(
            'INSERT INTO `%s` (`%s`) VALUES (\'%s\')',
            $table,
            implode('`, `', array_keys($data)),
            implode('\', \'', array_values($data))
        );

        try {
            $this->pdo->exec($query);
        } catch (Throwable $throwable) {
            // TODO: add exception
            return 0;
        }

        return (int) $this->pdo->lastInsertId();
    }

    public function update(string $table, int $id, array $data): bool
    {
        $update = '';
        foreach ($data as $key => $value) {
            if ($update !== '') {
                $update .= ', ';
            }

            $update .= sprintf('%s = \'%s\'', $key, $value);
        }

        $query = sprintf('UPDATE %s SET %s WHERE id = %d', $table, $update, $id);

        try {
            $this->pdo->exec($query);
        } catch (Throwable $throwable) {
            // TODO: add exception
            return false;
        }

        return true;
    }

    public function delete(string $table, int $id): bool
    {
        $query = sprintf('DELETE FROM %s WHERE id = %d', $table, $id);

        try {
            $this->pdo->exec($query);
        } catch (Throwable $throwable) {
            // TODO: add exception
            return false;
        }

        return true;
    }
}
