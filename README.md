Каркас проекта Cookie CMF
=========================

`cookyii/project` это каркас приложения [Yii 2](http://www.yiiframework.com/)
оптимизированный под горизонтальное масштабирование приложения.

Каркас включает базовые функции для работы cms,
а также предоставляет инфраструктуру для работы готовых модулей,
реализующий ту или иную функциональность.

[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)


Структура директорий
--------------------

    backend-app/            общий код приложения backend
    backend-modules/        модули приложения backend
    common/                 общие компоненты для всех приложений
    frontend-app/           общий код приложения frontend
    frontend-modules/       модули приложения frontend
    messages/               переводы приложений для всех приложений
    resources/              общие ресурсы (модели) для всех приложений
    runtime/                общие временны данные для всех приложений
    vendor/                 пакеты сторонних разработчиков



### Структура директорий внутри проложения

    backend-app/
        _assets/            ресурсы для внешнего вида приложения
            _sources/       исходники ресурсов, которые будут опубликованны в публичной части приложения
        commands/           контроллеры команды для выполнения в терминале (cli)
        components/         общие компоненты приложения
        config/             конфигурация приложения
        controllers/        базовые контроллеры приложения
        tests/              автоматические тесты приложения
        views/              общие представления (view) прилоения
        web/                публичная часть приложения, доступная из веба
        widgets/            общие виджеты приложения
        


### Структура директорий внутри модуля

    backend-modules/
        ModuleName/
            commands/           контроллеры команды для выполнения в терминале (cli)
            components/         компоненты модуля
            controllers/        контроллеры модуля
            views/              представления (view) модуля
            widgets/            виджеты модуля



Системные требования
--------------------

Минимальным требованием для работы этого каркаса является наличие PHP 5.5.0 или выше.


Установка
---------

Для начала необходимо установить `nodejs` и `npm`. Установка описана на [GitHub](https://github.com/joyent/node/wiki/Installation).

Если у Вас не установлен [Composer](http://getcomposer.org/), вы должны установить его.
Информацию об этом Вы можете получить на сайте [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).

### Установка через `composer`

Установить этот шаблон проекта Вы можете выполнив следующую команду:

```bash
composer global require "fxp/composer-asset-plugin:~1.0.0"
composer global require "cookyii/build:dev-master"
composer create-project --prefer-dist --stability=dev cookyii/project new-project
```

Далее Вам следует настроить виртуальные хосты Вашего Web сервера на следующие директории:

```
www.new-project.com      ->  .../frontend-app/web
backend.new-project.com  ->  .../backend-app/web
```


Развертывание Вашего проекта (deploy)
-------------------------------------

1. Скопировать файл `.env.dist` в `.env`, заполнить необходимые данные.
2. Скопировать файл `.credentials.env.dist` в `.credentials.env`, заполнить необходимые данные.
3. Установить `composer` зависимости `./build composer install-dev`. (для продакшена `./build composer install`)
4. Установить `frontend` зависимости через npm `./build npm`.
5. Скомпилировать `less` стили `./build less`.
6. Развернуть миграции `./build migrate`.
7. Обновить `rbac` правила `./build rbac`.


Настройка
---------

Вы можете изменять любые настройки в директориях `./common/config/`, `./frontend-app/config/`, `./backend-app/config/` и в конфигурации билда проекта.


Доступные команды `./build`
---------------------------

* `./build` или `./build set/dev` - собрать проект для dev площадки.
* `./build set/demo` - собрать проект для demo площадки.
* `./build set/production` - собрать проект для продакшена.

Дополнительно доступны следующие команды (они выполняются в рамках `set/*` команд, и сюда добавлены только для справки):
* `./build map` - показать список всех команд.
* `./build clear` - удалить все временные файлы и логи во всех приложениях.
* `./build clear/*` - удалить все временные файлы и логи в конкретном приложении.
* `./build migrate` - выполнить все новые миграции для всех приложений.
* `./build migrate/*` - выполнить все новые миграции для конкретного приложения.
* `./build less` - скомпилировать less для всех приложений.
* `./build less/*` - скомпилировать less для конкретного приложения.
* `./build composer` - установить `composer` зависимости из `composer.lock`.
* `./build composer/update` - скачать новые версии `composer` зависимостей и обновить `composer.lock`.
* `./build composer/selfupdate` - обновить `composer`.
* `./build rbac` - обновить правила `rbac` для всех приложений.
* `./build rbac/*` - обновить правила `rbac` для конкретного приложения.