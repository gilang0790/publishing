DIRECTORY STRUCTURE
-------------------

```
    /                   contains the entry script and web resources
    assets/             contains the web runtime assets
    assets_b/           contains application assets such as JavaScript and CSS
    commands/           contains console commands (controllers)
    config/             contains application configurations
    controllers/        contains Web controller classes
    mail/               contains view files for e-mails
    models/             contains model classes
    runtime/            contains files generated during runtime
    tests/              contains various tests for the yii2-practical-b application
    vendor/             contains dependent 3rd-party packages
    views/              contains view files for the Web application
```

REQUIREMENTS
------------

The minimum requirement by this project template that your Web server supports PHP 5.4.0.

CONFIGURATION
-------------

### Database

Edit the file `config/db.php` with real data, for example:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=yii2basic',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8',
];
```

**NOTES:**
- Yii won't create the database for you, this has to be done manually before you can access it.
- Check and edit the other files in the `config/` directory to customize your application as required.
- Refer to the README in the `tests` directory for information specific to basic application tests.
