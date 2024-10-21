<?php

namespace App\System\Database;

use PDO;
use PDOStatement;

  /**
   * Database interaction class.
   * 
   * @package System\Database
   */

abstract class DB {
    /**
     * PDO connection object.
     * @var PDO
     */
    private PDO $conn;

    /**
     * PDOStatement object.
     * @var PDOStatement
     */
    private PDOStatement $statement;

      /**
       * Constructor of the class DB.
       * Establishes connection with the
       *database server.
       */

    public function __construct() {
        $this->conn = new PDO("mysql:host=".config('db_host').";dbname=".config('db_name'), config('db_user'), config('db_pass'));
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

      /**
       *Executes the given database query.
       * 
       * @param string $sql
       * @return bool
       */

    public function run($sql): bool {
        $this->statement = $this->conn->prepare($sql);
        return $this->statement->execute();
    }
    /**
     * Fetches and returns data array
     * from the database query result.
     * 
     * @return array
     */
    public function fetch(): array {
        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Returns id of recently inserted data.
     * 
     * @return bool|string
     */
    public function insertId() {
        return $this->conn->lastInsertId();
    }
    /**
     * Returns the count of data fetched
     * from the database.
     * 
     * @return int
     */
    public function count(): int {
        return $this->statement->rowCount();
    }
      /**
       * Escapes and wraps the given value
       * with single quotes.
       * 
       * @param string $value
       * @return bool|string
       */

    public function quoteEscape(string $value){
        return $this->conn->quote($value);
    }
}