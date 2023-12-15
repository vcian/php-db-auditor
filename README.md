<p align="center"><img src="https://raw.githubusercontent.com/vcian/art/main/php-db-auditor/php-db-auditor-hr.svg" width="50%" alt="Logo PHP DB Auditor"></p>

![Packagist License](https://img.shields.io/packagist/l/vcian/php-db-auditor
)
[![Total Downloads](https://img.shields.io/packagist/dt/vcian/php-db-auditor
)](https://packagist.org/packages/vcian/php-db-auditor)

## Introduction

- Introducing "PHP DB Auditor" – your go-to solution for meticulous MySQL database system auditing. This powerful PHP package is your key to ensuring the utmost integrity and compliance with MySQL standards and constraints in your database. Dive into the world of database auditing with ease, thanks to its user-friendly command-line interface (CLI).

## Key Points

1. Comprehensive Auditing: PHP DB Auditor is a specialized PHP package designed to perform comprehensive audits on MySQL database systems.

2. Thorough Evaluation: It conducts a thorough and detailed evaluation of the entire MySQL database structure.

3. Standards Compliance: The package employs advanced scanning techniques to assess the database's adherence to MySQL standards and constraints.

4. Command-Line Interface (CLI): Users can effortlessly interact with PHP DB Auditor through its intuitive command-line interface, making the auditing process seamless.

5. Constraint Management: PHP DB Auditor empowers you to add essential constraints directly to your MySQL database via the CLI.

6. Detailed Audit Report: Upon completion of the auditing process, the package generates a detailed report.

7. Identifying Non-Compliance: The audit report provides a comprehensive list of tables and columns that fall short of meeting established MySQL standards and constraints.

8. Database Integrity: With PHP DB Auditor, you can proactively maintain the integrity of your database and ensure it aligns with the necessary standards.

## Installation

**Requires [PHP 8.1+](https://php.net/releases/)
#### **As a standalone project under linux**

    # create directory (feel free to give your own)
    mkdir dbauditor
    cd dbauditor

    # install package
    composer require vcian/php-db-auditor

    # create symbolic links
    ln -s vendor/vcian/php-db-auditor/dbauditor dbauditor
    ln -s vendor/vcian/php-db-auditor/src/config.php config.php

    # edit config.php to configure the database settings

#### **As a standalone project under windows**

    # create directory (feel free to give your own)
    mkdir dbauditor
    cd dbauditor

    # install package
    composer require vcian/php-db-auditor

    # create symbolic links
    mklink vendor/vcian/php-db-auditor/dbauditor dbauditor
    mklink vendor/vcian/php-db-auditor/src/config.php config.php

    # edit config.php to configure the database settings

## Usage:

#### **See database information**

    php dbauditor db:summary

<p align="center"><img src="https://raw.githubusercontent.com/vcian/art/main/php-db-auditor/db-auditor-summary.png" width="100%" alt="PHP DB Auditor Standard UI"></p>

#### **See a list of table with standard follow indication**

    php dbauditor db:standard

<p align="center"><img src="https://raw.githubusercontent.com/vcian/art/main/php-db-auditor/db-standard-ui.png" width="100%" alt="PHP DB Auditor Standard UI"></p>

<p align="center"><img src="https://raw.githubusercontent.com/vcian/art/main/php-db-auditor/db-standard-table-report-1.png" width="100%" alt="PHP DB Auditor Standard UI"></p>

<p align="center"><img src="https://raw.githubusercontent.com/vcian/art/main/php-db-auditor/db-standard-table-report-2.png" width="100%" alt="PHP DB Auditor Standard UI"></p>


#### **See a list of tables with fields, primary, foreign, unique, index constraint.**

    php dbauditor db:constraint

<p align="center"><img src="https://raw.githubusercontent.com/vcian/art/main/php-db-auditor/db-constraint-ui.png" width="100%" alt="PHP DB Auditor Constraint UI"></p>

<p align="center"><img src="https://raw.githubusercontent.com/vcian/art/main/php-db-auditor/db-constraint-add.png" width="100%" alt="PHP DB Auditor Constraint UI"></p>

You can add more constraint to a table.

<p align="center"><img src="https://raw.githubusercontent.com/vcian/art/main/php-db-auditor/db-constraint-selection.png" width="100%" alt="PHP DB Auditor Constraint UI"></p>

<p align="center"><img src="https://raw.githubusercontent.com/vcian/art/main/php-db-auditor/db-constraint-result.png" width="100%" alt="PHP DB Auditor Constraint UI"></p>

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