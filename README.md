# CLI-CRUD
CI4 Spark CLI Extension

Cli-Crud is an extension of CodeIgniter4 spark CLI. It will help you generate template files more quickly when developing projects with CodeIgniter4.

## Install

### Prerequisites
1. CodeIgniter Framework 4.*
2. Composer

### Composer Install

```
composer require rahpt/cli-crud
```

# Guide

## crud:controller

Create a new controller file.

* Use
    ```
    $ php spark create:controller [controller_name] [Options]
    ```

* Description:
    ```
    Create a new controller file.
    ```
* Arguments:
    1. controller_name : The controller name.

* Options:
    ```
    -nobase      Do not extends BaseControllers Class.
    -usemodel    Choose models.
    -space       Create folders and files according to the path you typed.
    ```

