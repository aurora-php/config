# Config

Configuration loading and handling component based on [Octris\PropertyCollection](https://github.com/octris/propertycollection).

## Usage

Example:

```php
<?php

require_once 'vendor/autoload.php';

use \Octris\Config;

$cfg = new Config([
    'common' => [
        'database' => [
            'adapter' => 'mysql',
            'username' => 'example',
            'password' => '12345678',
        ]
    ]
]);
$cfg->merge((new Reader\Yaml())->loadString(<<<YAML
common:
    database:
        adapter: postgres
YAML
));
$cfg->merge((new Reader\Yaml())->loadFileIfExists('/etc/octris/config_test.yml'));

$db = $cfg->get('common.database');
print $db->get('adapter') . "\n";
print $db->get('username') . "\n";

$db->set('password', strrev($db->get('password')));
print $cfg->get('common.database.password') . "\n";
```

Output:

    postgres
    example
    87654321
