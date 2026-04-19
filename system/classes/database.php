<?php
declare(strict_types=1);

/**
 * Kompatibilitäts-Wrapper: emuliert mysqli-Interface auf PDO-Basis.
 * Ermöglicht Betrieb ohne alle SQL-Calls in loginsystem.php gleichzeitig umzuschreiben.
 * $this->mysql->query("SELECT ...") funktioniert weiterhin, läuft aber intern über PDO.
 *
 * Copyright (C) 2026 Andreas P. <https://nfsmw15.de>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
class MysqliPDOWrapper
{
    private \PDO $pdo;
    public  int    $errno     = 0;
    public  string $error     = '';
    public  int    $insert_id = 0;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function query(string $sql): \MysqliResultWrapper|bool
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $this->errno     = 0;
            $this->error     = '';
            $this->insert_id = (int)$this->pdo->lastInsertId();

            $sqlTrimmed = ltrim($sql);
            $isSelect = stripos($sqlTrimmed, 'SELECT') === 0
                     || stripos($sqlTrimmed, 'SHOW')   === 0;
            if ($isSelect) {
                return new \MysqliResultWrapper($stmt);
            }
            return true;
        } catch (\PDOException $e) {
            $this->errno = (int)$e->getCode();
            $this->error = $e->getMessage();
            error_log('SQL-Fehler: ' . $e->getMessage());
            return false;
        }
    }

    public function real_escape_string(string $s): string
    {
        // Mit PDO Prepared Statements eigentlich nicht nötig – nur Kompatibilität
        return str_replace(["\\", "\0", "\n", "\r", "'", '"', "\x1a"],
                           ["\\\\", "\\0", "\\n", "\\r", "\\'", '\\"', "\\Z"], $s);
    }
}

/**
 * Emuliert mysqli_result auf PDO-Basis.
 */
class MysqliResultWrapper
{
    public  int   $num_rows = 0;
    private array $rows     = [];
    private int   $pos      = 0;

    public function __construct(\PDOStatement $stmt)
    {
        $this->rows     = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $this->num_rows = count($this->rows);
    }

    public function fetch_assoc(): ?array
    {
        return $this->rows[$this->pos++] ?? null;
    }

    public function fetch_array(int $mode = MYSQLI_BOTH): ?array
    {
        return $this->fetch_assoc();
    }
}

/**
 * Basis-Datenbankklasse mit PDO.
 */
class database
{
    protected \PDO              $pdo;
    protected \MysqliPDOWrapper $mysql; // Kompatibilitäts-Wrapper für loginsystem.php

    public function __construct()
    {
        global $pdo;
        if (!$pdo instanceof \PDO) {
            throw new \RuntimeException('Keine Datenbankverbindung. Bitte config.inc.php prüfen.');
        }
        $this->pdo   = $pdo;
        $this->mysql = new \MysqliPDOWrapper($pdo);
    }

    // ── Hilfsmethode: Prepared Query ────────────────────────────────────────

    protected function preparedQuery(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    // ── getTableInfo ─────────────────────────────────────────────────────────

    public function getTableInfo(string $table, ?string $value = null, string $prefix = Prefix): mixed
    {
        global $db_config;
        if (!empty($prefix)) $prefix .= '_';

        $stmt = $this->preparedQuery(
            "SHOW TABLE STATUS FROM `{$db_config['database']}` LIKE ?",
            [$prefix . $table]
        );
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($value !== null) {
            return $row[$value] ?? "Wert $value nicht gefunden!";
        }
        return $row;
    }

    // ── getAmount ────────────────────────────────────────────────────────────

    public function getAmount(
        string $table,
        array|string|null $column = null,
        mixed $value = null,
        string $operator = 'AND',
        string $prefix = Prefix
    ): int|string {
        if (!empty($prefix)) $prefix .= '_';
        $fullTable = $prefix . $table;

        [$whereSql, $params] = $this->buildWhere($column, $value, $operator);
        $stmt = $this->preparedQuery("SELECT COUNT(*) FROM `$fullTable`" . $whereSql, $params);
        return (int)$stmt->fetchColumn();
    }

    // ── getValue ─────────────────────────────────────────────────────────────

    public function getValue(
        string $table,
        array|string|null $column = null,
        mixed $value = null,
        array|string|null $output = null,
        bool $array = false,
        ?string $sort = null,
        string $operator = 'AND',
        string $prefix = Prefix
    ): mixed {
        if (!empty($prefix)) $prefix .= '_';
        $fullTable = $prefix . $table;

        [$whereSql, $params] = $this->buildWhere($column, $value, $operator);
        $limitSql = $array ? ($sort ? " $sort" : '') : ' LIMIT 1';
        $stmt     = $this->preparedQuery("SELECT * FROM `$fullTable`" . $whereSql . $limitSql, $params);

        if (!$array) {
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$row) return null;
            if ($output === null) return $row;
            if (is_array($output)) return array_intersect_key($row, array_flip($output));
            return $row[$output] ?? null;
        }

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if (empty($rows)) return null;
        if ($output === null) return $rows;
        return array_map(function ($row) use ($output) {
            if (is_array($output)) {
                return array_intersect_key($row, array_flip($output));
            }
            return $row[$output] ?? null;
        }, $rows);
    }

    // ── getMainData ───────────────────────────────────────────────────────────

    public function getMainData(string $tag): mixed
    {
        $stmt = $this->preparedQuery(
            "SELECT `value` FROM `" . Prefix . "_main` WHERE tag = ? LIMIT 1",
            [$tag]
        );
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? $row['value'] : "Einstellung nicht gefunden!";
    }

    // ── buildWhere (intern) ───────────────────────────────────────────────────

    private function buildWhere(
        array|string|null $column,
        mixed $value,
        string $operator
    ): array {
        if (empty($column)) return ['', []];

        $operator   = strtoupper($operator) === 'OR' ? 'OR' : 'AND';
        $conditions = [];
        $params     = [];

        if (is_array($column)) {
            foreach ($column as $i => $col) {
                $neg  = str_starts_with($col, '!');
                $col  = $neg ? substr($col, 1) : $col;
                $col  = trim($col, '`');
                if (str_contains($col, '.')) {
                    $parts = explode('.', $col);
                    $col = implode('.', array_map(fn($part) => '`'.trim($part, '`').'`', $parts));
                } else {
                    $col = '`'.$col.'`';
                }
                $val  = is_array($value) ? ($value[$i] ?? null) : $value;
                $conditions[] = $neg ? "$col != ?" : "$col = ?";
                $params[]     = $val;
            }
        } else {
            $neg    = str_starts_with($column, '!');
            $column = $neg ? substr($column, 1) : $column;
            $column = trim($column, '`');
            if (str_contains($column, '.')) {
                $parts = explode('.', $column);
                $column = implode('.', array_map(fn($part) => '`'.trim($part, '`').'`', $parts));
            } else {
                $column = '`'.$column.'`';
            }
            if (is_array($value)) {
                $phs          = implode(', ', array_fill(0, count($value), '?'));
                $conditions[] = $neg ? "$column NOT IN ($phs)" : "$column IN ($phs)";
                $params       = array_merge($params, $value);
            } else {
                $conditions[] = $neg ? "$column != ?" : "$column = ?";
                $params[]     = $value;
            }
        }

        return [' WHERE ' . implode(" $operator ", $conditions), $params];
    }

    // ─── mysqli-Kompatibilitäts-Wrapper (Migration zu PDO) ──────────────────────
    // Ermöglicht $this->mysql->query() Syntax über PDO-Backend
    // Wird entfernt sobald alle Queries auf Prepared Statements umgestellt sind

    protected function getMysqlCompat(): object
    {
        $pdo = $this->pdo;
        return new class($pdo) {
            public string $error = '';
            public int    $errno = 0;
            public int    $insert_id = 0;

            public function __construct(private ?\PDO $pdo) {}

            public function query(string $sql): object|bool
            {
                if (!$this->pdo) {
                    $this->error = 'Keine Datenbankverbindung';
                    $this->errno = 2006;
                    return false;
                }
                try {
                    $stmt = $this->pdo->query($sql);
                    $this->error    = '';
                    $this->errno    = 0;
                    $this->insert_id = (int)$this->pdo->lastInsertId();
                    return new class($stmt, $this->pdo) {
                        public int $num_rows;
                        private array $rows = [];
                        private int   $pos  = 0;

                        public function __construct(
                            private \PDOStatement $stmt,
                            private \PDO $pdo
                        ) {
                            $this->rows     = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                            $this->num_rows = count($this->rows);
                        }

                        public function fetch_assoc(): array|false
                        {
                            if ($this->pos >= count($this->rows)) return false;
                            return $this->rows[$this->pos++];
                        }

                        public function fetch_array(): array|false
                        {
                            return $this->fetch_assoc();
                        }
                    };
                } catch (\PDOException $e) {
                    $this->error = $e->getMessage();
                    $this->errno = (int)$e->getCode();
                    error_log('SQL-Fehler: ' . $e->getMessage() . ' | Query: ' . $sql);
                    return false;
                }
            }

            public function real_escape_string(string $str): string
            {
                // Mit PDO nicht nötig - Prepared Statements verwenden!
                return addslashes($str);
            }
        };
    }

}