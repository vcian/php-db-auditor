## PHP DB Auditor
## Introduction

- This package provides to audit process of reviewing and evaluating a mysql database system.
- DB Auditor scan your mysql database and give insights of mysql standards, constraints and provide option to add the constraints through CLI.
- The result of audit process shows list of tables & columns which doesn't have proper standards.

## Installation & Usage

> **Requires [PHP 8.1+](https://php.net/releases/)

You can download this package using git clone

    git clone "repo url"

After the cloned run the following commands

    cd php-db-auditor

Need to install dependency using [Composer](https://getcomposer.org):
> #### **composer install**

## Usage:
You can see DB Auditor commands using below command.

> #### **php app**
>
> This command provides a list of available commands for database selection, such as checking database standards or verifying constraints.
>
**Note:**

If you want to check standalone feature then you can execute below php command one by one.
> #### **php app db:standard**
>
> This command give you result with list of table with standard follow indication.
>
>

> #### **php app db:constraint**
>
> This command gives you result with list of tables with primary,foreign,unique,index constraint.
>
>
> You can add more constraint to the table by seeing existing constraint with table.