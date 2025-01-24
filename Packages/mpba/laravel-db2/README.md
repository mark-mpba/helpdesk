# laravel-db2
[![Latest Stable Version](https://poser.pugx.org/mpba/laravel-db2/v/stable)](https://packagist.org/packages/mpba/laravel-db2)
[![Total Downloads](https://poser.pugx.org/mpba/laravel-db2/downloads)](https://packagist.org/packages/mpba/laravel-db2)
[![Latest Unstable Version](https://poser.pugx.org/mpba/laravel-db2/v/unstable)](https://packagist.org/packages/mpba/laravel-db2)
[![License](https://poser.pugx.org/mpba/laravel-db2/license)](https://packagist.org/packages/mpba/laravel-db2)

laravel-db2 is a simple DB2 service provider for Laravel.
It provides DB2 Connection by extending the Illuminate Database component of the laravel framework.

---

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)

## Installation
Add laravel-db2 to your composer.json file:
```
"require": {
    "mpba/laravel-db2": "^8.0"
}
```
Use [composer](https://getcomposer.org) to install this package.
```
$ composer update
```

### Database Configuration
There are two ways to configure laravel-db2. You can choose the most convenient way for you. You can put your DB2 credentials into ``config/database.php`` (option 1) file or use package config file which you can generate through command line by artisan (option 2).

Please check appropriate specific DSN parameters for your connection.
For instance here are the ODBC keywords for IBMi
https://www.ibm.com/support/knowledgecenter/fr/ssw_ibm_i_74/rzaik/connectkeywords.htm

If you encounter issues with char fields containing characters outside the invariant character set (for example: "ü") in PHP applications using the UTF8 locale the workaround to prevent the extra garbage data is to set the following connection string keyword: ``DEBUG = 65536``

#### Option 1: Configure DB2 using ``config/database.php`` file
Simply add this code at the end of your ``config/database.php`` file:

```
<?php

/*
|--------------------------------------------------------------------------
| DB2 Config
|--------------------------------------------------------------------------
Valeurs définies pour le niveau d'isolation, pour PDO (mot clé "CMT" ou "CommitMode" dans le DSN) :
0 = Commit immediate (*NONE)
1 = Read committed (*CS)
2 = Read uncommitted (*CHG)
3 = Repeatable read (*ALL)
4 = Serializable (*RR)

* naming:
0 = "sql" (as in schema.table)
1 = "system" (as in schema/table)

* dateFormat:
0 = yy/dd (*JUL)
1 = mm/dd/yy (*MDY)
2 = dd/mm/yy (*DMY)
3 = yy/mm/dd (*YMD)
4 = mm/dd/yyyy (*USA)
5 = yyyy-mm-dd (*ISO)
6 = dd.mm.yyyy (*EUR)
7 = yyyy-mm-dd (*JIS)

* dateSeperator:
0 = "/" (forward slash)
1 = "-" (dash)
2 = "." (period)
3 = "," (comma)
4 = " " (blank)

* decimal:
0 = "." (period)
1 = "," (comma)

* timeFormat:
0 = hh:mm:ss (*HMS)
1 = hh:mm AM/PM (*USA)
2 = hh.mm.ss (*ISO)
3 = hh.mm.ss (*EUR)
4 = hh:mm:ss (*JIS)

* timeSeparator:
0 = ":" (colon)
1 = "." (period)
2 = "," (comma)
3 = " " (blank)

* PDO::ATTR_CASE:
PDO::CASE_LOWER
PDO::CASE_UPPER
PDO::CASE_NATURAL

* PDO::I5_ATTR_DBC_SYS_NAMING
true
false

* PDO::I5_ATTR_COMMIT
PDO::I5_TXN_READ_COMMITTED
PDO::I5_TXN_READ_UNCOMMITTED
PDO::I5_TXN_REPEATABLE_READ
PDO::I5_TXN_SERIALIZABLE
PDO::I5_TXN_NO_COMMIT

* PDO::I5_ATTR_DBC_LIBL
,
* PDO::I5_ATTR_DBC_CURLIB,

*/

return [

    'connections' => [

        'ibmi' => [
            'driver' => 'db2_ibmi_odbc',
            'driverName'=>'{IBM i Access ODBC Driver 64-bit}',
            'odbc_driver' => '/opt/ibm/iaccess/lib64/libcwbodbc.so',
            'dsn' => 'Driver={IBM i Access ODBC Driver 64-bit};System=192.168.0.81;AlwaysCalculateResultLength=1;CCSID=1208;NAM=1;DBQ=,'.env('DBQ'),
            'host' => env('DB_HOST', 'localhost'),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'prefix' => '',
            'schema' => 'ADC',
            'port' => 50000,
            'date_format' => 'Y-m-d H:i:s',
            'odbc_keywords' => [
                'SIGNON' => 3,
                'SSL' => 0,
                'CommitMode' => 2,
                'ConnectionType' => 0,
                'DefaultLibraries' => '',
                'Naming' => 0,
                'UNICODESQL' => 0,
                'DateFormat' => 5,
                'DateSeperator' => 0,
                'Decimal' => 0,
                'TimeFormat' => 0,
                'TimeSeparator' => 0,
                'TimestampFormat' => 0,
                'ConvertDateTimeToChar' => 0,
                'BLOCKFETCH' => 1,
                'BlockSizeKB' => 32,
                'AllowDataCompression' => 1,
                'CONCURRENCY' => 0,
                'LAZYCLOSE' => 0,
                'MaxFieldLength' => 15360,
                'PREFETCH' => 0,
                'QUERYTIMEOUT' => 1,
                'DefaultPkgLibrary' => 'QGPL',
                'DefaultPackage' => 'A /DEFAULT(IBM),2,0,1,0',
                'ExtendedDynamic' => 0,
                'QAQQINILibrary' => '',
                'SQDIAGCODE' => '',
                'LANGUAGEID' => 'ENU',
                'SORTTABLE' => '',
                'SortSequence' => 0,
                'SORTWEIGHT' => 0,
                'AllowUnsupportedChar' => 0,
                'CCSID' => 819,
                'GRAPHIC' => 0,
                'ForceTranslation' => 0,
                'ALLOWPROCCALLS' => 0,
                'DB2SQLSTATES' => 0,
                'DEBUG' => 0,
                'TRUEAUTOCOMMIT' => 0,
                'CATALOGOPTIONS' => 3,
                'LibraryView' => 0,
                'ODBCRemarks' => 0,
                'SEARCHPATTERN' => 1,
                'TranslationDLL' => '',
                'TranslationOption' => 0,
                'MAXTRACESIZE' => 0,
                'MultipleTraceFiles' => 1,
                'TRACE' => 0,
                'TRACEFILENAME' => '',
                'ExtendedColInfo' => 0,
            ],
            'options' => [
                    PDO::ATTR_CASE => PDO::CASE_NATURAL,
                    PDO::ATTR_PERSISTENT => false,
                ]
                + (defined('PDO::I5_ATTR_DBC_SYS_NAMING') ? [PDO::I5_ATTI5_ATTR_DBC_SYS_NAMINGR_COMMIT => false] : [])
                + (defined('PDO::I5_ATTR_COMMIT') ? [PDO::I5_ATTR_COMMIT => PDO::I5_TXN_NO_COMMIT] : [])
                + (defined('PDO::I5_ATTR_JOB_SORT') ? [PDO::I5_ATTR_JOB_SORT => false] : [])
                + (defined('PDO::I5_ATTR_DBC_LIBL') ? [PDO::I5_ATTR_DBC_LIBL => ''] : [])
                + (defined('PDO::I5_ATTR_DBC_CURLIB') ? [PDO::I5_ATTR_DBC_CURLIB => ''] : [])
        ],

    ],

];

```
driver setting can be:
- 'ibmi' for IBMi ODBC ICS connection
- 'db2_ibmi_odbc' for IBMi ODBC connection
- 'db2_ibmi_ibm' for IBMi PDO_IBM connection
- 'db2_zos_odbc' for zOS ODBC connection
- 'db2_expressc_odbc for Express-C ODBC connection

Then if driver is 'db2_*_odbc', database must be set to ODBC connection name.
if driver is 'db2_ibmi_ibm', database must be set to IBMi database name (WRKRDBDIRE).

#### Option 2: Configure DB2 using package config file

Run on the command line from the root of your project:

```
$ php artisan vendor:publish
```

Set your laravel-db2 credentials in ``config/db2.php``
the same way as above

### Queue Configuration
Simply set database connection driver value to ``'db2_odbc'`` in ``config/queue.php`` file:


## Usage

Consult the [Laravel framework documentation](https://laravel.com/docs).
