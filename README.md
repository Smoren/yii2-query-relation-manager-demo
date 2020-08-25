# QueryRelationManager demo

Демонстрация работы расширения **yii2-query-relation-manager**: 
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
Вывод:
```
Array
(
    [0] => Array
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

            [city] => Array
                (
                    [id] => 1
                    [name] => Moscow
                )

        )

    [1] => Array
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

            [city] => Array
                (
                    [id] => 1
                    [name] => Moscow
                )

        )

    [2] => Array
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

            [city] => Array
                (
                    [id] => 2
                    [name] => St. Petersburg
                )

        )

    [3] => Array
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

            [city] => Array
                (
                    [id] => 2
                    [name] => St. Petersburg
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

            [comments_count] => 2
            [mark_five_count] => 1
            [mark_average] => 4
        )

    [1] => Array
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

            [comments_count] => 1
            [mark_five_count] => 1
            [mark_average] => 5
        )

    [2] => Array
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

            [comments_count] => 1
            [mark_five_count] => 0
            [mark_average] => 4
        )

    [3] => Array
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
php yii test/provider
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