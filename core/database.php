<?php
/**
 * AeroTAXI - Database Layer (PDO)
 * Replaces Laravel's Eloquent ORM with raw PDO
 */

$__pdo = null;

/**
 * Get or create the PDO database connection (singleton)
 */
function db(): PDO
{
    global $__pdo;
    if ($__pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_DATABASE . ';charset=utf8mb4';
        $__pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $__pdo;
}

/**
 * Execute a query and return the PDOStatement
 */
function query(string $sql, array $params = []): PDOStatement
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Fetch a single row
 */
function fetch(string $sql, array $params = []): ?object
{
    $result = query($sql, $params)->fetch();
    return $result ?: null;
}

/**
 * Fetch all rows
 */
function fetchAll(string $sql, array $params = []): SimpleCollection
{
    return collect(query($sql, $params)->fetchAll());
}

/**
 * Insert a row and return the last insert ID
 */
function insert(string $table, array $data): string
{
    $columns = '`' . implode('`, `', array_keys($data)) . '`';
    $placeholders = implode(', ', array_fill(0, count($data), '?'));
    $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
    query($sql, array_values($data));
    return db()->lastInsertId();
}

/**
 * Update rows matching conditions
 */
function update(string $table, array $data, string $where, array $whereParams = []): int
{
    $set = implode(', ', array_map(fn($col) => "`{$col}` = ?", array_keys($data)));
    $sql = "UPDATE {$table} SET {$set} WHERE {$where}";
    $stmt = query($sql, array_merge(array_values($data), $whereParams));
    return $stmt->rowCount();
}

/**
 * Count rows
 */
function countRows(string $table, string $where = '1=1', array $params = []): int
{
    return (int) query("SELECT COUNT(*) as c FROM {$table} WHERE {$where}", $params)->fetch()->c;
}

/**
 * Sum a column
 */
function sumColumn(string $table, string $column, string $where = '1=1', array $params = []): float
{
    $result = query("SELECT COALESCE(SUM({$column}), 0) as s FROM {$table} WHERE {$where}", $params)->fetch();
    return (float) $result->s;
}

/**
 * Database Static Wrapper Class
 */
class Database
{
    public static function db(): PDO { return db(); }
    public static function query(string $sql, array $params = []): PDOStatement { return query($sql, $params); }
    public static function fetch(string $sql, array $params = []): ?object { return fetch($sql, $params); }
    public static function fetchAll(string $sql, array $params = []): SimpleCollection { return fetchAll($sql, $params); }
    public static function insert(string $table, array $data): string { return insert($table, $data); }
    public static function update(string $table, array $data, string $where, array $whereParams = []): int { return update($table, $data, $where, $whereParams); }
    public static function countRows(string $table, string $where = '1=1', array $params = []): int { return countRows($table, $where, $params); }
    public static function sumColumn(string $table, string $column, string $where = '1=1', array $params = []): float { return sumColumn($table, $column, $where, $params); }
}
