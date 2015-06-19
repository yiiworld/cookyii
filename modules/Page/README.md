Pages management module
=======================

Installation
------------

```bash
composer require cookyii/module-page:dev-master
```

Configuration
-------------

### 1. Update config
In `backend` `app` config
in section `modules` add `cookyii\modules\Page\backend\Module`
and in section `bootstrap` add `page`:
```php
// ./backend-app/config/app.php

return [
    // ...
    'bootstrap' => [
        // some components ...
        'page'
    ],
    'modules' => [
        // some modules ...
        'page' => 'cookyii\modules\Page\backend\Module',
    ],
    // ...
];
```

### 2. Execute new migrations
```bash
./frontend migrate
```

### 3. Add new permissions
In `rbac/update` command add merge class `cookyii\modules\Page\backend\Permissions`:
```php
// ./common/commands/RbacCommand.php

class RbacCommand extends \rmrevin\yii\rbac\Command
{
    
    public $backendMerge = [
        // ...
        'cookyii\modules\Page\backend\Permissions',
    ];
}

```

### 4. Update permissions
```bash
./backend rbac/update
```

### 5. Dependencies
Also, you need to configure the modules [account](https://github.com/cookyii/module-account)
and [media](https://github.com/cookyii/module-media) (they are already downloaded).