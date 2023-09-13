<?php

namespace Vcian\PhpDbAuditor\Traits;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Vcian\PhpDbAuditor\Constants\Constant;
use Vcian\PhpDbAuditor\Traits\DBConnection as DBConnectionService;
use Vcian\PhpDbAuditor\Traits\DBConstraint as DBConstraintService;

trait AuditService
{
    use DBConnectionService, DBConstraintService;

    /**
     * Get All Table List
     * @return array
     */
    public function getTablesList(): array
    {
        return $this->getTableList();
    }

    /**
     * Get Table Fields
     * @param string $tableName
     * @return array
     */
    public function getTableFields(string $tableName): array
    {
        return $this->getFieldsDetails($tableName);
    }

    /**
     * Get Table Size
     * @param string $tableName
     * @return string $size
     * @return string
     */
    public function getTablesSize(string $tableName): string
    {
        return $this->getTableSize($tableName);
    }

    /**
     * Check table exist or not
     * @param string $tableName
     * @return bool
     */
    public function checkTableExistOrNot(string $tableName): bool
    {
        return $this->checkTableExist($tableName);
    }

    /**
     * @param string $tableName
     * @param string $field
     * @return array
     */
    public function getFieldDataType(string $tableName, string $field): array
    {
        return $this->getFieldsDataType($tableName, $field);
    }

    /**
     * Check field exist or not
     * @param string $tableName
     * @param string $field
     * @return bool
     */
    public function checkFieldExistOrNot(string $tableName, string $field): bool
    {
        $fields = $this->getFields($tableName);
        if (in_array($field, $fields)) {
            return Constant::STATUS_TRUE;
        }
        return Constant::STATUS_FALSE;
    }

    /**
     * Get fields which has no constraint
     * @param string $tableName
     * @return array
     */
    public function getNoConstraintFields(string $tableName): array
    {
        $conn = createConnection();
        $fields = Constant::ARRAY_DECLARATION;
        try {
            // $fieldList = DB::select("SELECT * FROM `INFORMATION_SCHEMA`.`COLUMNS`
            // WHERE `TABLE_SCHEMA`= '" . env('DB_DATABASE') . "' AND `TABLE_NAME`= '" . $tableName . "' AND `COLUMN_KEY` = '' ");

            $query = $conn->query("SELECT * FROM `INFORMATION_SCHEMA`.`COLUMNS`
            WHERE `TABLE_SCHEMA`= '" . DB_NAME . "' AND `TABLE_NAME`= '" . $tableName . "' AND `COLUMN_KEY` = '' ");
            $fieldList = $query->fetch_all(MYSQLI_ASSOC);

            foreach ($fieldList as $field) {
                if (!in_array($field['DATA_TYPE'], Constant::RESTRICT_DATATYPE)) {
                    if (str_contains($field['DATA_TYPE'], "int")) {
                        $fields['integer'][] = $field['COLUMN_NAME'];
                    }
                    $fieldDetails = $this->getFieldDataType($tableName, $field['COLUMN_NAME']);

                    if ($fieldDetails['size'] <= Constant::DATATYPE_VARCHAR_SIZE) {
                        $fields['mix'][] = $field['COLUMN_NAME'];
                    }
                }
            }
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }
        return $fields;
    }

    /**
     * Get Constraint List
     * @param string $tableName
     * @param array $fields
     * @return array
     */
    public function getConstraintList(string $tableName, array $fields): array
    {
        $constrainList = Constant::ARRAY_DECLARATION;

        if (!empty($fields['integer'])) {

            if (!$this->tableHasValue($tableName)) {
                $constrainList[] = Constant::CONSTRAINT_FOREIGN_KEY;
            }

            if (empty($this->getConstraintField($tableName, Constant::CONSTRAINT_PRIMARY_KEY))) {
                $constrainList[] = Constant::CONSTRAINT_PRIMARY_KEY;
            }
        }

        if (!empty($fields['mix'])) {
            $constrainList[] = Constant::CONSTRAINT_INDEX_KEY;

            if (!empty($this->getUniqueFields($tableName, $fields['mix']))) {
                $constrainList[] = Constant::CONSTRAINT_UNIQUE_KEY;
            }
        }
        return $constrainList;
    }

    /**
     * Get constraint fields
     * @param string $tableName
     * @param string $input
     * @return array
     */
    public function getConstraintField(string $tableName, string $input): array
    {
        try {
            $conn = createConnection();
            $constraintFields = Constant::ARRAY_DECLARATION;

            if (!$this->checkTableExist($tableName)) {
                return [];
            }

            $query = $conn->query("SHOW KEYS FROM {$tableName} WHERE Key_name LIKE '%" . strtolower($input) . "%'");
            $result = $query->fetch_all(MYSQLI_ASSOC);

            if ($input === Constant::CONSTRAINT_FOREIGN_KEY) {
                return $this->getForeignKeyDetails($tableName);
            }

            if ($result) {
                foreach ($result as $value) {
                    $constraintFields[] = $value['Column_name'];
                }
            }
        } catch (Exception $exception) {
            error_log($exception->getMessage());
        }
        return $constraintFields;
    }

    /**
     * get Foreign Key
     * @param string $tableName
     * @return array
     */
    public function getForeignKeyDetails(string $tableName): array
    {
        $foreignFieldDetails = Constant::ARRAY_DECLARATION;
        try {
            $conn = createConnection();

            $query = $conn->query("SELECT i.TABLE_SCHEMA, i.TABLE_NAME, i.CONSTRAINT_TYPE,k.COLUMN_NAME, i.CONSTRAINT_NAME,
            k.REFERENCED_TABLE_NAME, k.REFERENCED_COLUMN_NAME FROM information_schema.TABLE_CONSTRAINTS i
            LEFT JOIN information_schema.KEY_COLUMN_USAGE k ON i.CONSTRAINT_NAME = k.CONSTRAINT_NAME
            WHERE i.CONSTRAINT_TYPE = 'FOREIGN KEY' AND i.TABLE_SCHEMA = '" . DB_NAME . "' AND i.TABLE_NAME = '" . $tableName . "'");

            $resultForeignKey = $query->fetch_all(MYSQLI_ASSOC);

            if ($resultForeignKey) {
                foreach ($resultForeignKey as $value) {
                    $foreignFieldDetails[] = [
                        "column_name" => $value['COLUMN_NAME'],
                        "foreign_table_name" => $value['REFERENCED_TABLE_NAME'],
                        "foreign_column_name" => $value['REFERENCED_COLUMN_NAME']
                    ];
                }
            }
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }
        return $foreignFieldDetails;
    }

    /**
     * Check Table Has Value
     * @param string $tableName
     * @return bool
     */
    public function tableHasValue(string $tableName): bool
    {
        try {
            $conn = createConnection();
            $result = $conn->query("Select * from " . $tableName);
            if ($result->num_rows > 0) {
                return Constant::STATUS_TRUE;
            }
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }
        return Constant::STATUS_FALSE;
    }

    /**
     * Add constraint to the field
     * @param string $table
     * @param string $field
     * @param string $constraint
     * @param string|null $referenceTableName
     * @param string|null $referenceField
     * @return bool
     */
    public function addConstraint(
        string $table, string $field,
        string $constraint, string $referenceTableName = null,
        string $referenceField = null): bool
    {
        try {
            switch ($constraint) {
                case Constant::CONSTRAINT_PRIMARY_KEY:
                    $this->migrateConstrain(Constant::CONSTRAINT_PRIMARY_KEY, $constraint, $table, $field);
                    break;
                case Constant::CONSTRAINT_INDEX_KEY:
                    $this->migrateConstrain(Constant::CONSTRAINT_INDEX_KEY, $constraint, $table, $field);
                    break;
                case Constant::CONSTRAINT_UNIQUE_KEY:
                    $this->migrateConstrain(Constant::CONSTRAINT_UNIQUE_KEY, $constraint, $table, $field);
                    break;
                case Constant::CONSTRAINT_FOREIGN_KEY:
                    $this->migrateConstrain(Constant::CONSTRAINT_FOREIGN_KEY, $constraint, $table, $field, $referenceField, $referenceTableName);
                    break;
                default:
                    return Constant::STATUS_FALSE;
            }
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }
        return Constant::STATUS_TRUE;
    }

    /**
     * Dynamic Migration
     * @param string $fileName
     * @param string $constrainName
     * @param string $tableName
     * @param string $fieldName
     * @param string|null $referenceField
     * @param string|null $referenceTableName
     * @return bool
     */
    public function migrateConstrain(
        string $fileName, string $constrainName,
        string $tableName, string $fieldName,
        string $referenceField = null, string $referenceTableName = null): bool
    {
        try {
            $fieldDetails = $this->getFieldDataType($tableName, $fieldName);
            $fieldDataType = Constant::NULL;

            if (!empty($fieldDetails['data_type'])) {
                $fieldDataType = Constant::MYSQL_DATATYPE_TO_LARAVEL_DATATYPE[$fieldDetails['data_type']] ?? $fieldDetails['data_type'];
            }

            $stubVariables = [
                "tableName" => $tableName,
                "fieldName" => $fieldName,
                "referenceField" => $referenceField,
                "referenceTable" => $referenceTableName,
                "dataType" => $fieldDataType
            ];

            $methodName = 'set'.ucfirst(strtolower($fileName)).'Constraint';

            if (method_exists($this, $methodName)) {
                $this->$methodName($stubVariables);
            } else {
                // Handle the case where the method doesn't exist
                echo "Method $methodName does not exist.";
            }

        } catch (Exception $exception) {
            error_log($exception->getMessage());
        }
        return Constant::STATUS_TRUE;
    }

    /**
     * Get Unique Fields
     * @param string $tableName
     * @param array $fields
     * @return array
     */
    public function getUniqueFields(string $tableName, array $fields): array
    {
        $uniqueField = Constant::ARRAY_DECLARATION;
        $conn = createConnection();
        try {
            foreach ($fields as $field) {
                $query = $conn->query("SELECT `" . $field . "`, COUNT(`" . $field . "`) as count FROM " . $tableName . " GROUP BY `" . $field . "` HAVING COUNT(`" . $field . "`) > 1");
                $result = $query->fetch_all(MYSQLI_ASSOC);

                if (empty($result)) {
                    $uniqueField[] = $field;
                }
            }

        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }
        return $uniqueField;
    }
}
