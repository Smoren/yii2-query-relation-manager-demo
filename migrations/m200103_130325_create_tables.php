<?php

use yii\db\Migration;

/**
 * Class m200103_130325_create_tables
 */
class m200103_130325_create_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('city', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ]);

        $this->batchInsert('city', ['id', 'name'], [
            [1, 'Moscow'],
            [2, 'St. Petersburg'],
            [3, 'Samara'],
            [4, 'Barnaul'],
            [5, 'Ivanovo'],
        ]);

        $this->createTable('address', [
            'id' => $this->primaryKey(),
            'city_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
        ]);

        $this->batchInsert('address', ['id', 'city_id', 'name'], [
            [1, 1, 'Tverskaya st., 7'],
            [2, 1, 'Schipok st., 1'],
            [3, 2, 'Mayakovskogo st., 12'],
            [4, 2, 'Galernaya st., 3'],
        ]);

        $this->createIndex('idx-address-city_id', 'address', 'city_id');
        $this->addForeignKey('fk-address-city_id-city-id', 'address', 'city_id', 'city', 'id', 'CASCADE');

        $this->createTable('place', [
            'id' => $this->primaryKey(),
            'address_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
        ]);

        $this->batchInsert('place', ['id', 'address_id', 'name'], [
            [1, 1, 'TC Tverskoy'],
            [2, 1, 'Tverskaya cafe'],
            [3, 2, 'Stasova music school'],
            [4, 3, 'Hostel on Mayakovskaya'],
            [5, 3, 'Mayakovskiy Store'],
            [6, 4, 'Cafe on Galernaya'],
        ]);

        $this->createIndex('idx-place-address_id', 'place', 'address_id');
        $this->addForeignKey('fk-place-address_id-address-id', 'place', 'address_id', 'address', 'id', 'CASCADE');

        $this->createTable('comment', [
            'id' => $this->primaryKey(),
            'place_id' => $this->integer()->notNull(),
            'username' => $this->string()->notNull(),
            'mark' => $this->tinyInteger()->notNull(),
            'text' => $this->text()->notNull(),
        ]);

        $this->createIndex('idx-comment-place_id', 'comment', 'place_id');
        $this->addForeignKey('fk-comment-place_id-place-id', 'comment', 'place_id', 'place', 'id', 'CASCADE');

        $this->batchInsert('comment', ['place_id', 'username', 'mark', 'text'], [
            [1, 'Ivan Mustafaevich', 3, 'Not bad, not good'],
            [1, 'Peter', 5, 'Good place'],
            [1, 'Mark', 1, 'Bad place'],
            [3, 'Ann', 5, 'The best music school!'],
            [5, 'Stas', 4, 'Rather good place'],
            [6, 'Stas', 3, 'Small menu, long wait'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-comment-place_id-place-id', 'comment');
        $this->dropForeignKey('fk-place-address_id-address-id', 'place');
        $this->dropForeignKey('fk-address-city_id-city-id', 'address');

        $this->dropTable('comment');
        $this->dropTable('place');
        $this->dropTable('address');
        $this->dropTable('city');
    }
}
