<?php

namespace Vcian\PhpDbAuditor\Traits;

use Exception;
use Vcian\PhpDbAuditor\Constants\Constant;

trait DBConstraint
{
    private static $initialized = false;
    private static $conn;

    /**
     * Initialize the trait (constructor-like behavior).
     */
    private static function initializeTrait()
    {
        if (!self::$initialized) {
            // create a database connection
            self::$conn = createConnection();
            self::$initialized = true;
        }
    }

    public function __call($name, $arguments)
    {
        self::initializeTrait();

        if (method_exists($this, $name)) {
            return call_user_func_array([$this, $name], $arguments);
        } else {
            throw new Exception("Method $name not found in trait " . __CLASS__);
        }
    }
    /**
     * Set Index Key Constraint
     * @param string $tableName
     * @param string $fieldName
     * @return array|bool
     */
    public function setIndexConstraint(string $tableName, string $fieldName): bool
    {
        $sql = "CREATE INDEX idx_".$fieldName." ON ".$tableName." (".$fieldName.");";
        $conn = createConnection();
        if ($conn->query($sql) === Constant::STATUS_TRUE) {
            return Constant::STATUS_TRUE;
        } else {
            // echo "Error creating index: " . self::$conn->error;
            return Constant::STATUS_FALSE;
        }
    }
    /**
     * Set Unique Key Constraint
     * @param string $tableName
     * @param string $fieldName
     * @return array|bool
     */
    public function setUniqueConstraint(string $tableName, string $fieldName): bool
    {
        $sql = "CREATE UNIQUE INDEX uk_".$fieldName." ON ".$tableName." (".$fieldName.");";
        $conn = createConnection();
        if ($conn->query($sql) === Constant::STATUS_TRUE) {
            return Constant::STATUS_TRUE;
        } else {
            // echo "Error creating index: " . self::$conn->error;
            return Constant::STATUS_FALSE;
        }
    }

    /**
     * Set Primary Key Constraint
     * @param string $tableName
     * @param string $fieldName
     * @return array|bool
     */
    public function setPrimaryConstraint(string $tableName, string $fieldName): bool
    {
        $sql = "ALTER TABLE ".$tableName." ADD PRIMARY KEY (".$fieldName.");";
        $conn = createConnection();
        if ($conn->query($sql) === Constant::STATUS_TRUE) {
            return Constant::STATUS_TRUE;
        } else {
            // echo "Error creating index: " . self::$conn->error;
            return Constant::STATUS_FALSE;
        }
    }

    /**
     * Set Foreign Key Constraint
     * @param string $tableName
     * @param string $fieldName
     * @return array|bool
     */
    public function setForeignConstraint(string $tableName, string $fieldName): bool
    {
        $sql = "ALTER TABLE ".$tableName." ADD PRIMARY KEY (".$fieldName.");";
        $conn = createConnection();
        if ($conn->query($sql) === Constant::STATUS_TRUE) {
            return Constant::STATUS_TRUE;
        } else {
            // echo "Error creating index: " . self::$conn->error;
            return Constant::STATUS_FALSE;
        }
    }
}