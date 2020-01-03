# QueryRelationManager demo

Демонстрация работы расширения **yii2-query-relation-manager**
https://github.com/Smoren/yii2-query-relation-manager

### Install
```
composer install
php yii migrate
```

### Demo

Выбираем адреса с городом, местами и комментариями о местах
```
php yii test/address
```

Выбираем места с адресом и городом, а также комментариями, причем:
- комментарии имеют оценку не ниже 3
- если подходящих комментариев нет, место не попадает в выборку (inner join)
- для каждого места считаем количество комментариев, количество оценок "5" и среднюю оценку среди оценок не ниже 3
```
php yii test/place
```

Получаем города из списка ID с адресами
```
php yii test/city
```