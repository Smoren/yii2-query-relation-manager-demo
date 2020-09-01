# QueryRelationManager demo

Демонстрация работы расширения **yii2-query-relation-manager**: 
https://github.com/Smoren/yii2-query-relation-manager

Установка расширения через composer:
```
composer require smoren/yii2-query-relation-manager
``` 

### Install
```
composer install
php yii migrate
```

### Unit testing
```
./vendor/bin/codecept run unit tests/unit
```

### Demo

Выбираем адреса с городом, местами и комментариями о местах
```
php yii test/address
```
Вывод:
```
Array
(
    [0] => Array
        (
            [id] => 1
            [city_id] => 1
            [name] => Tverskaya st., 7
            [city] => Array
                (
                    [id] => 1
                    [name] => Moscow
                )

            [places] => Array
                (
                    [0] => Array
                        (
                            [id] => 1
                            [address_id] => 1
                            [name] => TC Tverskoy
                            [comments] => Array
                                (
                                    [0] => Array
                                        (
                                            [id] => 1
                                            [place_id] => 1
                                            [username] => Ivan Mustafaevich
                                            [mark] => 3
                                            [text] => Not bad, not good
                                        )

                                    [1] => Array
                                        (
                                            [id] => 2
                                            [place_id] => 1
                                            [username] => Peter
                                            [mark] => 5
                                            [text] => Good place
                                        )

                                    [2] => Array
                                        (
                                            [id] => 3
                                            [place_id] => 1
                                            [username] => Mark
                                            [mark] => 1
                                            [text] => Bad place
                                        )

                                )

                        )

                    [1] => Array
                        (
                            [id] => 2
                            [address_id] => 1
                            [name] => Tverskaya cafe
                            [comments] => Array
                                (
                                )

                        )

                )

        )

    [1] => Array
        (
            [id] => 2
            [city_id] => 1
            [name] => Schipok st., 1
            [city] => Array
                (
                    [id] => 1
                    [name] => Moscow
                )

            [places] => Array
                (
                    [0] => Array
                        (
                            [id] => 3
                            [address_id] => 2
                            [name] => Stasova music school
                            [comments] => Array
                                (
                                    [0] => Array
                                        (
                                            [id] => 4
                                            [place_id] => 3
                                            [username] => Ann
                                            [mark] => 5
                                            [text] => The best music school!
                                        )

                                )

                        )

                )

        )

    [2] => Array
        (
            [id] => 3
            [city_id] => 2
            [name] => Mayakovskogo st., 12
            [city] => Array
                (
                    [id] => 2
                    [name] => St. Petersburg
                )

            [places] => Array
                (
                    [0] => Array
                        (
                            [id] => 4
                            [address_id] => 3
                            [name] => Hostel on Mayakovskaya
                            [comments] => Array
                                (
                                )

                        )

                    [1] => Array
                        (
                            [id] => 5
                            [address_id] => 3
                            [name] => Mayakovskiy Store
                            [comments] => Array
                                (
                                    [0] => Array
                                        (
                                            [id] => 5
                                            [place_id] => 5
                                            [username] => Stas
                                            [mark] => 4
                                            [text] => Rather good place
                                        )

                                )

                        )

                )

        )

    [3] => Array
        (
            [id] => 4
            [city_id] => 2
            [name] => Galernaya st., 3
            [city] => Array
                (
                    [id] => 2
                    [name] => St. Petersburg
                )

            [places] => Array
                (
                    [0] => Array
                        (
                            [id] => 6
                            [address_id] => 4
                            [name] => Cafe on Galernaya
                            [comments] => Array
                                (
                                    [0] => Array
                                        (
                                            [id] => 6
                                            [place_id] => 6
                                            [username] => Stas
                                            [mark] => 3
                                            [text] => Small menu, long wait
                                        )

                                )

                        )

                )

        )

)
```

Выбираем места с адресом и городом, а также комментариями, причем:
- комментарии имеют оценку не ниже 3
- если подходящих комментариев нет, место не попадает в выборку (inner join)
- для каждого места считаем количество комментариев, количество оценок "5" и среднюю оценку среди оценок не ниже 3
```
php yii test/place
```
Вывод:
```
Array
(
    [0] => Array
        (
            [id] => 1
            [address_id] => 1
            [name] => TC Tverskoy
            [address] => Array
                (
                    [id] => 1
                    [city_id] => 1
                    [name] => Tverskaya st., 7
                    [city] => Array
                        (
                            [id] => 1
                            [name] => Moscow
                        )

                )

            [comments] => Array
                (
                    [0] => Array
                        (
                            [id] => 1
                            [place_id] => 1
                            [username] => Ivan Mustafaevich
                            [mark] => 3
                            [text] => Not bad, not good
                        )

                    [1] => Array
                        (
                            [id] => 2
                            [place_id] => 1
                            [username] => Peter
                            [mark] => 5
                            [text] => Good place
                        )

                )

            [comments_count] => 2
            [mark_five_count] => 1
            [mark_average] => 4
        )

    [1] => Array
        (
            [id] => 3
            [address_id] => 2
            [name] => Stasova music school
            [address] => Array
                (
                    [id] => 2
                    [city_id] => 1
                    [name] => Schipok st., 1
                    [city] => Array
                        (
                            [id] => 1
                            [name] => Moscow
                        )

                )

            [comments] => Array
                (
                    [0] => Array
                        (
                            [id] => 4
                            [place_id] => 3
                            [username] => Ann
                            [mark] => 5
                            [text] => The best music school!
                        )

                )

            [comments_count] => 1
            [mark_five_count] => 1
            [mark_average] => 5
        )

    [2] => Array
        (
            [id] => 5
            [address_id] => 3
            [name] => Mayakovskiy Store
            [address] => Array
                (
                    [id] => 3
                    [city_id] => 2
                    [name] => Mayakovskogo st., 12
                    [city] => Array
                        (
                            [id] => 2
                            [name] => St. Petersburg
                        )

                )

            [comments] => Array
                (
                    [0] => Array
                        (
                            [id] => 5
                            [place_id] => 5
                            [username] => Stas
                            [mark] => 4
                            [text] => Rather good place
                        )

                )

            [comments_count] => 1
            [mark_five_count] => 0
            [mark_average] => 4
        )

    [3] => Array
        (
            [id] => 6
            [address_id] => 4
            [name] => Cafe on Galernaya
            [address] => Array
                (
                    [id] => 4
                    [city_id] => 2
                    [name] => Galernaya st., 3
                    [city] => Array
                        (
                            [id] => 2
                            [name] => St. Petersburg
                        )

                )

            [comments] => Array
                (
                    [0] => Array
                        (
                            [id] => 6
                            [place_id] => 6
                            [username] => Stas
                            [mark] => 3
                            [text] => Small menu, long wait
                        )

                )

            [comments_count] => 1
            [mark_five_count] => 0
            [mark_average] => 3
        )

)
```

Получаем города из списка ID с адресами
```
php yii test/city
```
Вывод:
```
Array
(
    [0] => Array
        (
            [id] => 3
            [name] => Samara
            [addresses] => Array
                (
                )

        )

    [1] => Array
        (
            [id] => 2
            [name] => St. Petersburg
            [addresses] => Array
                (
                    [0] => Array
                        (
                            [id] => 3
                            [city_id] => 2
                            [name] => Mayakovskogo st., 12
                        )

                    [1] => Array
                        (
                            [id] => 4
                            [city_id] => 2
                            [name] => Galernaya st., 3
                        )

                )

        )

)
```

Используем QueryRelationDataProvider для пагинации
```
php yii provider/city
```
Вывод:
```
Array
(
    [0] => Array
        (
            [id] => 1
            [name] => Moscow
            [addresses] => Array
                (
                    [0] => Array
                        (
                            [id] => 2
                            [city_id] => 1
                            [name] => Schipok st., 1
                        )

                    [1] => Array
                        (
                            [id] => 1
                            [city_id] => 1
                            [name] => Tverskaya st., 7
                        )

                )

        )

    [1] => Array
        (
            [id] => 2
            [name] => St. Petersburg
            [addresses] => Array
                (
                    [0] => Array
                        (
                            [id] => 4
                            [city_id] => 2
                            [name] => Galernaya st., 3
                        )

                    [1] => Array
                        (
                            [id] => 3
                            [city_id] => 2
                            [name] => Mayakovskogo st., 12
                        )

                )

        )

)
```

Используем упрощенный синтаксис построения запросов QueryRelationDataProvider
с исполльзованием метода QueryRelationDataProvider::with()

Выбираем города с адресами и местами
```
php yii with/city
```
Вывод:
```
Array
(
    [0] => Array
        (
            [id] => 1
            [name] => Moscow
            [addresses] => Array
                (
                    [0] => Array
                        (
                            [id] => 2
                            [city_id] => 1
                            [name] => Schipok st., 1
                            [places] => Array
                                (
                                    [0] => Array
                                        (
                                            [id] => 3
                                            [address_id] => 2
                                            [name] => Stasova music school
                                        )

                                )

                        )

                    [1] => Array
                        (
                            [id] => 1
                            [city_id] => 1
                            [name] => Tverskaya st., 7
                            [places] => Array
                                (
                                    [0] => Array
                                        (
                                            [id] => 1
                                            [address_id] => 1
                                            [name] => TC Tverskoy
                                        )

                                    [1] => Array
                                        (
                                            [id] => 2
                                            [address_id] => 1
                                            [name] => Tverskaya cafe
                                        )

                                )

                        )

                )

        )

    [1] => Array
        (
            [id] => 2
            [name] => St. Petersburg
            [addresses] => Array
                (
                    [0] => Array
                        (
                            [id] => 4
                            [city_id] => 2
                            [name] => Galernaya st., 3
                            [places] => Array
                                (
                                    [0] => Array
                                        (
                                            [id] => 6
                                            [address_id] => 4
                                            [name] => Cafe on Galernaya
                                        )

                                )

                        )

                    [1] => Array
                        (
                            [id] => 3
                            [city_id] => 2
                            [name] => Mayakovskogo st., 12
                            [places] => Array
                                (
                                    [0] => Array
                                        (
                                            [id] => 4
                                            [address_id] => 3
                                            [name] => Hostel on Mayakovskaya
                                        )

                                    [1] => Array
                                        (
                                            [id] => 5
                                            [address_id] => 3
                                            [name] => Mayakovskiy Store
                                        )

                                )

                        )

                )

        )

    [2] => Array
        (
            [id] => 3
            [name] => Samara
            [addresses] => Array
                (
                )

        )

    [3] => Array
        (
            [id] => 4
            [name] => Barnaul
            [addresses] => Array
                (
                )

        )

    [4] => Array
        (
            [id] => 5
            [name] => Ivanovo
            [addresses] => Array
                (
                )

        )

)
```

Используем упрощенный синтаксис построения запросов QueryRelationDataProvider
с исполльзованием метода QueryRelationDataProvider::with()

Выбираем адреса с городом, местами и комментариями, оценка которых не ниже трех
```
php yii with/address
```
Вывод:
```
Array
(
    [0] => Array
        (
            [id] => 1
            [city_id] => 1
            [name] => Tverskaya st., 7
            [city] => Array
                (
                    [id] => 1
                    [name] => Moscow
                )

            [places] => Array
                (
                    [0] => Array
                        (
                            [id] => 1
                            [address_id] => 1
                            [name] => TC Tverskoy
                            [comments] => Array
                                (
                                    [0] => Array
                                        (
                                            [id] => 1
                                            [place_id] => 1
                                            [username] => Ivan Mustafaevich
                                            [mark] => 3
                                            [text] => Not bad, not good
                                        )

                                    [1] => Array
                                        (
                                            [id] => 2
                                            [place_id] => 1
                                            [username] => Peter
                                            [mark] => 5
                                            [text] => Good place
                                        )

                                )

                        )

                    [1] => Array
                        (
                            [id] => 2
                            [address_id] => 1
                            [name] => Tverskaya cafe
                            [comments] => Array
                                (
                                )

                        )

                )

        )

    [1] => Array
        (
            [id] => 2
            [city_id] => 1
            [name] => Schipok st., 1
            [city] => Array
                (
                    [id] => 1
                    [name] => Moscow
                )

            [places] => Array
                (
                    [0] => Array
                        (
                            [id] => 3
                            [address_id] => 2
                            [name] => Stasova music school
                            [comments] => Array
                                (
                                    [0] => Array
                                        (
                                            [id] => 4
                                            [place_id] => 3
                                            [username] => Ann
                                            [mark] => 5
                                            [text] => The best music school!
                                        )

                                )

                        )

                )

        )

    [2] => Array
        (
            [id] => 3
            [city_id] => 2
            [name] => Mayakovskogo st., 12
            [city] => Array
                (
                    [id] => 2
                    [name] => St. Petersburg
                )

            [places] => Array
                (
                    [0] => Array
                        (
                            [id] => 4
                            [address_id] => 3
                            [name] => Hostel on Mayakovskaya
                            [comments] => Array
                                (
                                )

                        )

                    [1] => Array
                        (
                            [id] => 5
                            [address_id] => 3
                            [name] => Mayakovskiy Store
                            [comments] => Array
                                (
                                    [0] => Array
                                        (
                                            [id] => 5
                                            [place_id] => 5
                                            [username] => Stas
                                            [mark] => 4
                                            [text] => Rather good place
                                        )

                                )

                        )

                )

        )

    [3] => Array
        (
            [id] => 4
            [city_id] => 2
            [name] => Galernaya st., 3
            [city] => Array
                (
                    [id] => 2
                    [name] => St. Petersburg
                )

            [places] => Array
                (
                    [0] => Array
                        (
                            [id] => 6
                            [address_id] => 4
                            [name] => Cafe on Galernaya
                            [comments] => Array
                                (
                                    [0] => Array
                                        (
                                            [id] => 6
                                            [place_id] => 6
                                            [username] => Stas
                                            [mark] => 3
                                            [text] => Small menu, long wait
                                        )

                                )

                        )

                )

        )

)
```

Профилирование и сравнение скорости и корректности работы стандартного подхода
ActiveQuery::joinWith() и QueryRelationManager
```
php yii profile/address
php yii profile/provider
```

Используем QueryRelationDataProvider с PDO вместо ORM

Выбираем адреса с городом, местами и комментариями о местах
```
php yii pdo/address
```
Вывод:
```
Array
(
    [0] => Array
        (
            [id] => 1
            [city_id] => 1
            [name] => Tverskaya st., 7
            [city] => Array
                (
                    [id] => 1
                    [name] => Moscow
                )

            [places] => Array
                (
                    [0] => Array
                        (
                            [id] => 1
                            [address_id] => 1
                            [name] => TC Tverskoy
                            [comments] => Array
                                (
                                    [0] => Array
                                        (
                                            [id] => 1
                                            [place_id] => 1
                                            [username] => Ivan Mustafaevich
                                            [mark] => 3
                                            [text] => Not bad, not good
                                        )

                                    [1] => Array
                                        (
                                            [id] => 2
                                            [place_id] => 1
                                            [username] => Peter
                                            [mark] => 5
                                            [text] => Good place
                                        )

                                    [2] => Array
                                        (
                                            [id] => 3
                                            [place_id] => 1
                                            [username] => Mark
                                            [mark] => 1
                                            [text] => Bad place
                                        )

                                )

                        )

                    [1] => Array
                        (
                            [id] => 2
                            [address_id] => 1
                            [name] => Tverskaya cafe
                            [comments] => Array
                                (
                                )

                        )

                )

        )

    [1] => Array
        (
            [id] => 2
            [city_id] => 1
            [name] => Schipok st., 1
            [city] => Array
                (
                    [id] => 1
                    [name] => Moscow
                )

            [places] => Array
                (
                    [0] => Array
                        (
                            [id] => 3
                            [address_id] => 2
                            [name] => Stasova music school
                            [comments] => Array
                                (
                                    [0] => Array
                                        (
                                            [id] => 4
                                            [place_id] => 3
                                            [username] => Ann
                                            [mark] => 5
                                            [text] => The best music school!
                                        )

                                )

                        )

                )

        )

    [2] => Array
        (
            [id] => 3
            [city_id] => 2
            [name] => Mayakovskogo st., 12
            [city] => Array
                (
                    [id] => 2
                    [name] => St. Petersburg
                )

            [places] => Array
                (
                    [0] => Array
                        (
                            [id] => 4
                            [address_id] => 3
                            [name] => Hostel on Mayakovskaya
                            [comments] => Array
                                (
                                )

                        )

                    [1] => Array
                        (
                            [id] => 5
                            [address_id] => 3
                            [name] => Mayakovskiy Store
                            [comments] => Array
                                (
                                    [0] => Array
                                        (
                                            [id] => 5
                                            [place_id] => 5
                                            [username] => Stas
                                            [mark] => 4
                                            [text] => Rather good place
                                        )

                                )

                        )

                )

        )

    [3] => Array
        (
            [id] => 4
            [city_id] => 2
            [name] => Galernaya st., 3
            [city] => Array
                (
                    [id] => 2
                    [name] => St. Petersburg
                )

            [places] => Array
                (
                    [0] => Array
                        (
                            [id] => 6
                            [address_id] => 4
                            [name] => Cafe on Galernaya
                            [comments] => Array
                                (
                                    [0] => Array
                                        (
                                            [id] => 6
                                            [place_id] => 6
                                            [username] => Stas
                                            [mark] => 3
                                            [text] => Small menu, long wait
                                        )

                                )

                        )

                )

        )

)
```
