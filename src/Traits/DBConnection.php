<?php

namespace Vcian\PhpDbAuditor\Traits;

use Exception;
use Vcian\PhpDbAuditor\Constants\Constant;

trait DBConnection
{

    /**
     * Get Table List
     * @return array
     */
    public function getTableList(): array
    {
        // Use the createConnection() function to get the MYSQL instance
        $conn = createConnection();
        $tableList = Constant::ARRAY_DECLARATION;
        try {
            // Execute the query to fetch all tables
            $query = $conn->query('SHOW TABLES');

            // Fetch the results as an associative array
            $tables = $query->fetch_all(MYSQLI_ASSOC);

            if ($tables) {
                foreach ($tables as $tableValue) {
                    foreach ($tableValue as $tableName) {
                        $tableList[] = $tableName;
                    }
                }
            }
        } catch (Exception $exception) {
            error_log($exception->getMessage());;
        }
        return $tableList;
    }

    /**
     * Get list of datatype of field and name of the field by table name
     * @param string $tableName
     * @return array $fields
     */
    public function getFields(string $tableName): array
    {
        $conn = createConnection();
        $fields = Constant::ARRAY_DECLARATION;
        try {
            $fieldDetails = $conn->query("Describe `$tableName`");
            foreach ($fieldDetails as $field) {
                $fields[] = $field['Field'];
            }
        } catch (Exception $exception) {
            error_log($exception->getMessage());;
        }
        return $fields;
    }

    /**
     * Check Table exist or not in the database
     * @param string $tableName
     * @return bool
     */
    public function checkTableExist(string $tableName): bool
    {
        try {
            $tables = $this->getTableList();

            if (in_array($tableName, $tables)) {
                return Constant::STATUS_TRUE;
            }
        } catch (Exception $exception) {
            error_log($exception->getMessage());
        }
        return Constant::STATUS_FALSE;
    }

    /**
     * Get field with type by table
     * @param string $tableName
     * @return array
     */
    public function getFieldsDetails(string $tableName): array
    {
        $fieldWithType = Constant::ARRAY_DECLARATION;
        try {
            $conn = createConnection();
            $query = "SELECT * FROM `INFORMATION_SCHEMA`.`COLUMNS`
                        WHERE `TABLE_SCHEMA`= '" . $this->getDatabaseName() . "' AND `TABLE_NAME`= '" . $tableName . "' ";
            $query = $conn->query($query);
            // Fetch the results as an associative array
            $fieldWithType = $query->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $exception) {
            error_log($exception->getMessage());;
        }
        return $fieldWithType;
    }

    /**
     * Get Table Size
     * @param string $tableName
     * @return string
     */
    public function getTableSize(string $tableName): string
    {
        try {
            $conn = createConnection();
            $query = 'SELECT
                    ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024),2) AS `size` FROM information_schema.TABLES
                    WHERE
                        TABLE_SCHEMA = "' . $this->getDatabaseName() . '" AND TABLE_NAME = "' . $tableName . '"
                    ORDER BY
                        (DATA_LENGTH + INDEX_LENGTH) DESC';
            $query = $conn->query($query);

            // Fetch the results as an associative array
            $result = $query->fetch_all(MYSQLI_ASSOC);
            if (isset($result) && !empty($result)) {
                return $result[0]['size'];
            }

        } catch (Exception $exception) {
            error_log($exception->getMessage());
        }
        return Constant::NULL;
    }

    /**
     * Get Table Engine
     * @param string $tableName
     */
    public function getTableEngine(string $tableName)
    {
        try {
            $conn = createConnection();
            $query = 'SELECT engine FROM information_schema.Tables where TABLE_SCHEMA = "'. $this->getDatabaseName() .'" AND TABLE_NAME = "' . $tableName . '" Limit 1';

            $query = $conn->query($query);
            $result = $query->fetch_assoc();

            if (isset($result['ENGINE']) && !empty($result['ENGINE'])) {
                return $result['ENGINE'];
            }

        } catch (Exception $exception) {
            error_log($exception->getMessage());
        }
        return Constant::NULL;
    }

    /**
     * Get Field Data Type
     * @param string $tableName
     * @param string $fieldName
     * @return array|bool
     */
    public function getFieldDataType(string $tableName, string $fieldName): array|bool
    {
        try {
            $conn = createConnection();
            $query = "SELECT `DATA_TYPE`, `CHARACTER_MAXIMUM_LENGTH`, `NUMERIC_PRECISION`, `NUMERIC_SCALE`  FROM `INFORMATION_SCHEMA`.`COLUMNS`
            WHERE `TABLE_SCHEMA`= '" . $this->getDatabaseName() . "' AND `TABLE_NAME`= '" . $tableName . "' AND `COLUMN_NAME` = '" . $fieldName . "' ";

            $query = $conn->query($query);
            $dataType = $query->fetch_assoc();
            if(in_array($dataType['DATA_TYPE'], Constant::NUMERIC_DATATYPE)) {

                if($dataType['DATA_TYPE'] === Constant::DATATYPE_DECIMAL) {
                    $size = "(". $dataType['NUMERIC_PRECISION'] .",". $dataType['NUMERIC_SCALE'] .")";
                } else {
                    $size = $dataType['NUMERIC_PRECISION'];
                }
            } else {
                $size = $dataType['CHARACTER_MAXIMUM_LENGTH'];
            }

            if (isset($dataType['DATA_TYPE']) && $dataType !== null) {
                return ['data_type' => $dataType['DATA_TYPE'], 'size' => $size];
            }
        } catch (Exception $exception) {
            error_log($exception->getMessage());;
        }
        return Constant::STATUS_FALSE;
    }

    /**
     * Get Field Data Type
     * @param string $tableName
     * @param string $fieldName
     * @return array|bool
     */
    public function getFieldsDataType(string $tableName, string $fieldName): array|bool
    {
        try {
            $conn = createConnection();

            $query = "SELECT `DATA_TYPE`, `CHARACTER_MAXIMUM_LENGTH`, `NUMERIC_PRECISION`, `NUMERIC_SCALE`  FROM `INFORMATION_SCHEMA`.`COLUMNS`
            WHERE `TABLE_SCHEMA`= '" . $this->getDatabaseName() . "' AND `TABLE_NAME`= '" . $tableName . "' AND `COLUMN_NAME` = '" . $fieldName . "' ";

            $query = $conn->query($query);
            $dataType = $query->fetch_assoc();

            if (isset($dataType['DATA_TYPE']) && $dataType !== null) {
                return ['data_type' => $dataType['DATA_TYPE'], 'size' => $dataType['CHARACTER_MAXIMUM_LENGTH'] ];
            }
        } catch (Exception $exception) {
            error_log($exception->getMessage());
        }
        return Constant::STATUS_FALSE;
    }

    public function getDatabaseName()
    {
        $conn = createConnection();
        $databaseName = $conn->query("SELECT DATABASE()")->fetch_row()[0];
        return $databaseName;
    }

    public function getDatabaseSize()
    {
        try {
            $conn = createConnection();
            $query = 'SELECT table_schema as db_name, ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) "size"
                FROM information_schema.tables
                where table_schema = "'. $this->getDatabaseName() .'" GROUP BY table_schema';

            $query = $conn->query($query);
            $result = $query->fetch_assoc();
            if ($result) {
                return $result['size'];
            }
        } catch (Exception $exception) {
            error_log($exception->getMessage());
        }
        return Constant::NULL;

    }

    public function getDatabaseEngin()
    {
        try {
            $conn = createConnection();
            $query = 'SELECT engine FROM information_schema.Tables where TABLE_SCHEMA = "'. $this->getDatabaseName() .'" Limit 1';

            $query = $conn->query($query);
            $result = $query->fetch_assoc();

            if (isset($result['ENGINE']) && !empty($result['ENGINE'])) {
                return $result['ENGINE'];
            }

        } catch (Exception $exception) {
            error_log($exception->getMessage());
        }
        return Constant::NULL;
    }

    public function getCharacterSetName()
    {
        try {

            $conn = createConnection();
            $query = 'SELECT DEFAULT_CHARACTER_SET_NAME
            FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = "'. $this->getDatabaseName() .'"';

            $query = $conn->query($query);
            $result = $query->fetch_assoc();

            if (isset($result['DEFAULT_CHARACTER_SET_NAME']) && !empty($result['DEFAULT_CHARACTER_SET_NAME'])) {
                return $result['DEFAULT_CHARACTER_SET_NAME'];
            }

        } catch (Exception $exception) {
            error_log($exception->getMessage());
        }
        return Constant::NULL;
    }
}