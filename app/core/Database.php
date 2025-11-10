<?php

trait Database
{
    private function connect(): PDO
    {
        static $pdo = null;

        if ($pdo === null) {
            try {

                $dbPath = DirROOT . "/database.db";

                $pdo = new PDO("sqlite:" . $dbPath, null, null, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
                // Flash::set('toast', "Database connection successful", 'success');
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
                Flash::set('toast', "Database connection failed: " . $e->getMessage(), 'danger');
            }
        }

        return $pdo;
    }


    public function query(string $query, array $data = []): array|false
    {
        try {
            $stmt = $this->connect()->prepare($query);

            // SQLite requires explicit type binding for LIMIT/OFFSET
            foreach ($data as $key => $value) {
                $stmt->bindValue(":" . $key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }

            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("SQLite Query Error: " . $e->getMessage());
            return false;
        }
    }

    public function execute(string $query, array $data = []): bool
    {
        try {
            $stmt = $this->connect()->prepare($query);

            foreach ($data as $key => $value) {
                $stmt->bindValue(":" . $key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("SQLite Execute Error: " . $e->getMessage());
            return false;
        }
    }

    public function lastInsertId(): string
    {
        return $this->connect()->lastInsertId();
    }
}
