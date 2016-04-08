# Личный кабинет

#### Installation
##### Требования для установки
- php 5.4 и выше
- установленная БД

##### Порядок установки
- если используется nginx, то прописать параметры из файлы ```nginx```
- загрузить все submodules:
```
git submodule init
git submodule update --recursive
```
- загрузить все сторонние пакеты:
```
cd protected
./composer.phar selfupdate
./composer.phar install
```
- прописать локальные параметры окружения в protected/config/params.php
- прописать нужные команды в cron из файлы ```crontab```
- запустить все команды из cron
- запустить миграции данных
```
cd protected
./yiic migrate
```

--

#### Кодовый стандарт
Для PHP используется стандарт PSR-0.

Основные настройки:
- использование **UpperCamelCase** для классов и **lowerCamelCase** для функций и переменных
- **пробелы** вместо табуляций
- **4 пробела** на табуляцию
- **константы** только в верхнем регистре, слова разделены подчеркиванием.
- **отделение оператора** от скобок одним пробелом
- отделение операторов **сравнения** и **логических** **операторов** от аргументов пробелами с двух сторон
- **скобка** **для** **блоков** на той же строке, отделена одним пробелом
- **скобка для функций и классов** на новой строке
- использование **осмысленных названий** и без сокращений для функций, методов, классов, и переменных

```
Пример:

class HelloWorld
{
    const TYPE_A = 1;

    public static function hello($something)
    {
        if ($something == self::TYPE_A || $something == 2) {
            // do smth
        } else {
            // do smth other
        }
    }
}
```

--

#### Работа с LESS/CSS

В проекте не рекомендуется редактирование CSS-файлов напрямую.
Вместо этого необходимо создавать LESS-файл и настроить любой удобный автоматический компилятор в CSS.
```
Рекомендуемая структура директорий:

/ParentFolder
  -/css
    Здесь будут лежать CSS-файлы
  -/less
    Здесь лежат LESS-файлы
  -/views
    Здесь лежат шаблоны, подключающие скомпилированные CSS
```

Использование BEM-стиля запрещено (если только это не внешняя CSS).
Необходимо использовать изолированный каскадный стиль.
Каждый шаблон ДОЛЖЕН включать в себя корневой div-элемент с уникальным названием.
Это позволит изолировать стили разных блоков друг от друга.
```
Twig-шаблон:
{{ import('css', 'path.to.css.myWidget') }}

<div class='my-widget'>
   <div class='title'></div>
   <div class='content'></div>
   ... smth else
</div>


LESS <path/to/css/myWidget.less>:
.my-widget {
    .title {...}
    .content {...}
}
```

