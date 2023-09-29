<p align="center"><img src="https://raw.githubusercontent.com/vcian/art/main/php-db-auditor/php-db-auditor-hr.svg" width="50%" alt="Logo PHP DB Auditor"></p>

![Packagist License](https://img.shields.io/packagist/l/vcian/php-db-auditor?style=for-the-badge)
[![Total Downloads](https://img.shields.io/packagist/dt/vcian/php-db-auditor?style=for-the-badge)](https://packagist.org/packages/vcian/php-db-auditor)


## Introduction

- This package provides to audit process of reviewing and evaluating a mysql database system.
- DB Auditor scan your mysql database and give insights of mysql standards, constraints and provide option to add the constraints through CLI.
- The result of audit process shows list of tables & columns which doesn't have proper standards.

## Installation & Usage

**Requires [PHP 8.1+](https://php.net/releases/)

Need to install dependency using [Composer](https://getcomposer.org):
#### **composer install**

## Usage:
You can see DB Auditor commands using below command.

#### **php dbauditor**

    This command provides a list of available commands for database selection, such as checking database standards or verifying constraints.

**Note:**

If you want to check standalone feature then you can execute below php command one by one.
#### **php dbauditor db:standard**

    This command give you result with list of table with standard follow indication.

<p align="center"><img src="https://raw.githubusercontent.com/vcian/art/main/php-db-auditor/db-standard-ui.png" width="100%" alt="PHP DB Auditor Standard UI"></p>

<p align="center"><img src="https://raw.githubusercontent.com/vcian/art/main/php-db-auditor/db-standard-table-report-1.png" width="100%" alt="PHP DB Auditor Standard UI"></p>

<p align="center"><img src="https://raw.githubusercontent.com/vcian/art/main/php-db-auditor/db-standard-table-report-2.png" width="100%" alt="PHP DB Auditor Standard UI"></p>


#### **php dbauditor db:constraint**

    This command gives you result with list of tables with primary,foreign,unique,index constraint.

<p align="center"><img src="https://raw.githubusercontent.com/vcian/art/main/php-db-auditor/db-constraint-ui.png" width="100%" alt="PHP DB Auditor Constraint UI"></p>

<p align="center"><img src="https://raw.githubusercontent.com/vcian/art/main/php-db-auditor/db-constraint-add.png" width="100%" alt="PHP DB Auditor Constraint UI"></p>

<p align="center"><img src="https://raw.githubusercontent.com/vcian/art/main/php-db-auditor/db-constraint-selection.png" width="100%" alt="PHP DB Auditor Constraint UI"></p>

<p align="center"><img src="https://raw.githubusercontent.com/vcian/art/main/php-db-auditor/db-constraint-result.png" width="100%" alt="PHP DB Auditor Constraint UI"></p>


You can add more constraint to the table by seeing existing constraint with table.
#### **php dbauditor db:summary**

    This command provides you with information about the database, including its name, size, table count, and character set.

<p align="center"><img src="https://raw.githubusercontent.com/vcian/art/main/php-db-auditor/db-auditor-summary.png" width="100%" alt="PHP DB Auditor Standard UI"></p>

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

       We believe in 
            👇
          ACT NOW
      PERFECT IT LATER
    CORRECT IT ON THE WAY.

## Security

If you discover any security-related issues, please email ruchit.patel@viitor.cloud instead of using the issue tracker.

## Credits

- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.