<?php

trait Model
{
    use Database;

    protected int $limit = 10;
    protected int $offset = 0;
    protected string $order_type = 'asc';
    protected string $order_column = 'id';
    protected array $allowedOrderTypes = ['asc', 'desc'];
    // protected array $allowedOrderColumns = ['id'];
    // protected array $allowedColumns = [];

    public array $errors = [];

    protected function sanitizeOrder(): void
    {
        $this->order_type = strtolower($this->order_type);

        if (!in_array($this->order_type, $this->allowedOrderTypes)) {
            $this->order_type = 'asc';
        }

        if (empty($this->allowedOrderColumns)) {
            $this->allowedOrderColumns = ['id'];
        }

        if (!in_array($this->order_column, $this->allowedOrderColumns)) {
            $this->order_column = $this->allowedOrderColumns[0];
        }
    }

    public function setPagination(int $limit, int $offset = 0): void
    {
        $this->limit = max(1, $limit);
        $this->offset = max(0, $offset);
    }

    public function findAll(int $limit = 0): array|false
    {
        if ($limit > 0) {
            $this->limit = $limit;
        }

        $this->sanitizeOrder();

        $query = sprintf(
            "SELECT * FROM %s ORDER BY %s %s LIMIT :limit OFFSET :offset",
            $this->table,
            $this->order_column,
            strtoupper($this->order_type)
        );

        $result = $this->query($query, [
            'limit'  => $this->limit,
            'offset' => $this->offset
        ]);

        return $result ?: false;
    }

    public function countWhere(array $where = [], array $date = []): int
    {
        $conditions = [];
        $params = [];

        foreach ($where as $col => $val) {
            $conditions[] = "$col = :$col";
            $params[$col] = $val;
        }

        if (!empty($date)) {
            foreach ($date as $col => $val) {
                $conditions[] = "DATE($col) = :$col";
                $params[$col] = $val;
            }
        }

        $whereClause = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
        $query = "SELECT COUNT(*) AS count FROM {$this->table} $whereClause";

        $result = $this->query($query, $params);

        return (int)($result[0]['count'] ?? 0);
    }

    public function countAll(): int
    {
        $query = "SELECT COUNT(*) AS count FROM {$this->table}";
        $result = $this->query($query);
        return (int)($result[0]['count'] ?? 0);
    }

    public function where(array $data = [], array $data_not = []): array
    {
        $this->sanitizeOrder();

        $conditions = [];
        $params = [];

        foreach ($data as $key => $value) {
            $conditions[] = "$key = :$key";
            $params[$key] = $value;
        }

        foreach ($data_not as $key => $value) {
            $conditions[] = "$key != :$key";
            $params[$key] = $value;
        }

        $whereClause = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $query = sprintf(
            "SELECT * FROM %s %s ORDER BY %s %s LIMIT :limit OFFSET :offset",
            $this->table,
            $whereClause,
            $this->order_column,
            strtoupper($this->order_type)
        );

        $params['limit']  = $this->limit;
        $params['offset'] = $this->offset;

        $result = $this->query($query, $params);

        return is_array($result) ? $result : [];
    }

    public function first(array $data = [], array $data_not = []): array|false
    {
        $conditions = [];
        $params = [];

        foreach ($data as $key => $value) {
            $conditions[] = "$key = :$key";
            $params[$key] = $value;
        }

        foreach ($data_not as $key => $value) {
            $conditions[] = "$key != :$key";
            $params[$key] = $value;
        }

        $whereClause = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $query = sprintf("SELECT * FROM %s %s LIMIT 1", $this->table, $whereClause);

        $result = $this->query($query, $params);

        return $result[0] ?? false;
    }

    public function insert(array $data): int|false
    {
        $data = array_filter(
            $data,
            fn($key) => empty($this->allowedColumns) || in_array($key, $this->allowedColumns),
            ARRAY_FILTER_USE_KEY
        );

        if (empty($data)) {
            $this->addError('No allowed columns present.');
            return false;
        }

        $keys = array_keys($data);

        $query = sprintf(
            "INSERT INTO %s (%s) VALUES (:%s)",
            $this->table,
            implode(", ", $keys),
            implode(", :", $keys)
        );

        if ($this->execute($query, $data)) {
            return (int)$this->lastInsertId();
        }

        return false;
    }

    public function update($id, array $data, string $id_column = 'id'): bool
    {
        $data = array_filter(
            $data,
            fn($key) => empty($this->allowedColumns) || in_array($key, $this->allowedColumns),
            ARRAY_FILTER_USE_KEY
        );

        if (empty($data)) {
            $this->addError('No allowed columns found in data.');
            return false;
        }

        $setParts = array_map(fn($key) => "$key = :$key", array_keys($data));

        $query = sprintf(
            "UPDATE %s SET %s WHERE %s = :%s",
            $this->table,
            implode(", ", $setParts),
            $id_column,
            $id_column
        );

        $data[$id_column] = $id;

        return $this->execute($query, $data);
    }

    public function delete($id, string $id_column = 'id'): bool
    {
        $query = sprintf(
            "DELETE FROM %s WHERE %s = :%s",
            $this->table,
            $id_column,
            $id_column
        );

        return $this->execute($query, [$id_column => $id]);
    }

    public function exists(array $data): bool
    {
        return (bool)$this->first($data);
    }

    public function addError(string|array $error): void
    {
        $this->errors = array_merge($this->errors, (array)$error);
    }

    public function showErrors(): array
    {
        $flattened = [];

        foreach ($this->errors as $error) {
            if (is_array($error)) {
                $flattened = array_merge($flattened, $error);
            } else {
                $flattened[] = $error;
            }
        }

        return $flattened;
    }
}
