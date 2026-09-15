<?php

declare(strict_types=1);

/**
 * @package         Subway
 * @version         0.1.1
 * @authors         Kant (Aldus)
 * @license         CC BY-SA 4.0
 * @license_terms   https://creativecommons.org/licenses/by-sa/4.0/
 * @platform        WBCE 1.6.x
 * @requirements    PHP 8.4.x (8.3 recommented)
 */

namespace wbstats\core;

use Exception;
use InvalidArgumentException;
use const TABLE_PREFIX;

class Database
{
    public const string DO_UPDATE = "update";
    public const string DO_INSERT = "insert";
    
    /** @var object|null */
    public static ?object $instance = null;

    protected const string STR_EXCEPTION = "EXCEPTION: %s";
    protected const string STR_STATEMENT = "STATEMENT: %s";
    
    /**
     * Get or initialize the singleton database instance.
     */
    public static function getInstance(): object
    {
        return static::$instance ??= $GLOBALS['database'];
    }

    /**
     * Execute a single MySQL query.
     */
    public static function executeQuery(
        string $aQuery = "",
        bool $bFetch = false,
        array &$aStorage = [],
        bool $bFetchAll = true
    ): int {
        $instance = self::getInstance();
        self::handleTableprefix($aQuery);

        try {
            $statement = $instance->db_handle->prepare($aQuery);
            $statement->execute();
            $result = $statement->get_result();

            if (!$result) {
                return -1;
            }
            
            if ($bFetch && $result->num_rows > 0) {
                $aStorage = $bFetchAll
                    ? $result->fetch_all(MYSQLI_ASSOC)
                    : $result->fetch_assoc();
            }

            return $result->num_rows;
        } catch(Exception $error) {
            self::logError($error->getMessage(), $aQuery, $instance->db_handle, "[1]");
            return -1;
        }
    }

    /**
     * Perform a simple query and return results as array.
     */
    public static function query(string $query, array $params = []): mixed
    {
        $instance = self::getInstance();

        $retVal = [];

        self::handleTableprefix($query);

        try {
            $statement = $instance->db_handle->prepare($query);
            $statement->execute($params);
            $result = $statement->get_result();

            if (!$result)
            {
                return $statement->affected_rows;
            }
            
            if ($result->num_rows > 0)
            {
                $retVal = $result->fetch_all(MYSQLI_ASSOC);
            }

            return $retVal;
        } catch(Exception $error) {
            self::logError($error->getMessage(), $query, $instance->db_handle, "[3]");
            return false;
        }
    }

    public static function fetchValue(string $query, array $params): mixed
    {
        $result = self::query($query, $params);
        if (is_array($result))
        {
            if (isset($result[0]) && is_array($result[0]))
            {
                return array_shift($result[0]);
            }
            return $result[0] ?? null;
        }
        return null;
    }

    /**
     * Update or insert database records.
     *
     * @throws InvalidArgumentException
     */
    public static function update(string $what, string $table, array $values, string|array $where = ""): bool
    {
        $instance = self::getInstance();
        self::handleTableprefix($table);
        self::testTablename($table);

        $query = match(strtolower($what)) {
            self::DO_UPDATE => self::buildUpdateQuery($table, $values, $where),
            self::DO_INSERT => self::buildInsertQuery($table, $values),
            default => throw new InvalidArgumentException(
                "[Subway!] Not correct job in " . __CLASS__ . " in " . __LINE__ . ". Passed: " . $what,
                40067
            ),
        };

        try {
            $statement = $instance->db_handle->prepare($query['sql']);
            $statement->execute(array_values($query['values']));
            return true;
        } catch(Exception $error) {
            self::logError($error->getMessage(), $query['sql'], $instance->db_handle, "[2]");
            return false;
        }
    }

    /**
     * Build UPDATE query with WHERE conditions.
     */
    private static function buildUpdateQuery(string $table, array &$values, string|array $where): array
    {
        $query = "UPDATE `" . $table . "` SET ";
        $query .= implode(", ", array_map(fn($field) => "`$field` = ?", array_keys($values)));

        if (is_array($where)) {
            $result = self::buildWhereCondition($where);
            $query .= " WHERE " . $result['mysql'];
            $values = array_merge(array_values($values), $result['params']);
        } elseif ($where !== "") {
            $query .= " WHERE " . $where;
        }

        return ['sql' => $query, 'values' => $values];
    }

    /**
     * Build INSERT query.
     */
    private static function buildInsertQuery(string $table, array $values): array
    {
        $keys = array_keys($values);
        $query = "INSERT INTO `" . $table . "` (`" . implode("`,`", $keys) . "`) VALUES ("
               . substr(str_repeat("?, ", count($values)), 0, -2) . ")";
        
        return ['sql' => $query, 'values' => $values];
    }

    /**
     * Drop table from database.
     */
    public static function drop(string $table): bool
    {
        self::handleTableprefix($table);
        self::testTablename($table);
        self::query("DROP TABLE `" . $table . "` IF EXISTS");
        return true;
    }

    /**
     * Validate table name to prevent SQL injection.
     *
     * @throws InvalidArgumentException
     */
    public static function testTablename(string $table): bool
    {
        if (!preg_match('/^[\w]+$/i', $table)) {
            throw new InvalidArgumentException(
                "[Subway!] Invalid table name: " . $table,
                40067
            );
        }
        return true;
    }

    /**
     * Execute multiple queries.
     */
    public static function handleJobs(array $jobs = []): void
    {
        foreach ($jobs as $queryStr) {
            self::query($queryStr);
        }
    }

    /**
     * Replace {TP} and {TABLE_PREFIX} placeholders.
     */
    public static function handleTableprefix(string &$tablename): void
    {
        $tablename = str_replace(['{TP}', '{TABLE_PREFIX}'], TABLE_PREFIX, $tablename);
    }

    /**
     * Build MySQL field list with optional prefix.
     */
    public static function prepareFields(array $fields = [], ?string $prefix = null): string
    {
        $prefixWithDot = ($prefix ? $prefix . "." : "");
        return empty($fields)
            ? $prefixWithDot . "*"
            : $prefixWithDot . "`" . implode("`, " . $prefixWithDot . "`", $fields) . "`";
    }

    /**
     * Build WHERE conditions from nested array structure.
     */
    public static function buildWhereCondition(array $values, int $deep = 0): array
    {
        $mySQLstr = "";
        $params = [];
        
        foreach ($values as $term) {
            if (isset($term[0])) {
                $new = self::buildWhereCondition($term, $deep + 1);
                $mySQLstr .= $new['mysql'];
                $params = array_merge($params, $new['params']);
                continue;
            }
            
            $mySQLstr .= sprintf(
                "%s `%s` %s ? ",
                $term['concat'] ?? "AND",
                $term['field'],
                $term['compare'] ?? "="
            );

            $test = filter_var($term['value'], FILTER_VALIDATE_INT);
            $params[] = $test !== false ? $test : $term['value'];
        }
        
        if ($deep === 0) {
            $mySQLstr = substr($mySQLstr, 4);
        } else {
            $mySQLstr = substr($mySQLstr, 0, 3) . "(" . substr($mySQLstr, 3) . ")";
        }
        
        return ["mysql" => $mySQLstr, "params" => $params];
    }

    /**
     * Log database errors consistently.
     */
    private static function logError(string $message, string $query, object $handle, string $code): void
    {
        trigger_error(sprintf(self::STR_EXCEPTION . " " . $code, $message));
        trigger_error(sprintf(self::STR_EXCEPTION, mysqli_error($handle)));
        trigger_error(sprintf(self::STR_STATEMENT, preg_replace('/\s+/', ' ', $query)));
        self::$instance->set_error(sprintf(self::STR_EXCEPTION, mysqli_error($handle)));
    }

    /**
     * Protect the class of getting instance use within "new".
     * Protect the constructor.
     */
    protected function __construct() {
        // Nothing to do here.
    }
}
