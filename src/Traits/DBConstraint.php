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
     * @param array $stubVariables
     * @return array|bool
     */
    public function setIndexConstraint(array $stubVariables): bool
    {
        $sql = "CREATE INDEX idx_".$stubVariables['fieldName']." ON ".$stubVariables['tableName']." (".$stubVariables['fieldName'].");";
        $conn = createConnection();
        if ($conn->query($sql) === Constant::STATUS_TRUE) {
            return Constant::STATUS_TRUE;
        } else {
            return Constant::STATUS_FALSE;
        }
    }
    /**
     * Set Unique Key Constraint
     * @param string $tableName
     * @param array $stubVariables
     * @return array|bool
     */
    public function setUniqueConstraint(array $stubVariables): bool
    {
        $sql = "CREATE UNIQUE INDEX uk_".$stubVariables['fieldName']." ON ".$stubVariables['tableName']." (".$stubVariables['fieldName'].");";
        $conn = createConnection();
        if ($conn->query($sql) === Constant::STATUS_TRUE) {
            return Constant::STATUS_TRUE;
        } else {
            return Constant::STATUS_FALSE;
        }
    }

    /**
     * Set Primary Key Constraint
     * @param string $tableName
     * @param array $stubVariables
     * @return array|bool
     */
    public function setPrimaryConstraint(array $stubVariables): bool
    {
        $sql = "ALTER TABLE ".$stubVariables['tableName']." ADD PRIMARY KEY (".$stubVariables['fieldName'].");";
        $conn = createConnection();
        if ($conn->query($sql) === Constant::STATUS_TRUE) {
            return Constant::STATUS_TRUE;
        } else {
            return Constant::STATUS_FALSE;
        }
    }

    /**
     * Set Foreign Key Constraint
     * @param string $tableName
     * @param array $stubVariables
     * @return array|bool
     */
    public function setForeignConstraint(array $stubVariables): bool
    {
        $sql = "ALTER TABLE ".$stubVariables['tableName']."
                ADD CONSTRAINT fk_".$stubVariables['fieldName']."
                FOREIGN KEY (".$stubVariables['fieldName'].")
                REFERENCES ".$stubVariables['referenceTable']." (".$stubVariables['referenceField'].");";
        $conn = createConnection();
        if ($conn->query($sql) === Constant::STATUS_TRUE) {
            return Constant::STATUS_TRUE;
        } else {
            return Constant::STATUS_FALSE;
        }
    }
}