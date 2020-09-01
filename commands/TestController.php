<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Address;
use app\models\City;
use app\models\Comment;
use app\models\Place;
use Smoren\Yii2\QueryRelationManager\Yii2\QueryRelationManager;
use Smoren\Yii2\QueryRelationManager\Base\QueryRelationManagerException;
use yii\console\Controller;
use yii\db\Query;

/**
 * Контроллер для демонстрации работы QueryRelationManager
 * @package app\commands
 * @author Smoren <ofigate@gmail.com>
 */
class TestController extends Controller
{
    /**
     * Выбираем адреса с городом, местами и комментариями о местах
     * @throws QueryRelationManagerException
     */
    public function actionAddress()
    {
        $result = QueryRelationManager::select(Address::class, 'a')
            ->withSingle('city', City::class, 'c', 'a', ['id' =>  'city_id'])
            ->withMultiple('places', Place::class, 'p', 'a', ['address_id' =>  'id'])
            ->withMultiple('comments', Comment::class, 'cm', 'p', ['place_id' =>  'id'])
            ->all();

        print_r($result);
    }

    /**
     * Выбираем места с адресом и городом, а также комментариями, причем:
     *  - комментарии имеют оценку не ниже 3
     *  - если подходящих комментариев нет, место не попадает в выборку (inner join)
     *  - для каждого места считаем количество комментариев, количество оценок "5" и среднюю оценку среди оценок не ниже 3
     * @throws QueryRelationManagerException
     */
    public function actionPlace()
    {
        $result = QueryRelationManager::select(Place::class, 'p')
            ->withSingle('address', Address::class, 'a', 'p', ['id' => 'address_id'])
            ->withSingle('city', City::class, 'c', 'a', ['id' => 'city_id'])
            ->withMultiple('comments', Comment::class, 'cm', 'p', ['place_id' => 'id'],
                'inner', 'and cm.mark >= :mark', [':mark' => 3])
            ->modify('p', function(array &$place) {
                $place['comments_count'] = count($place['comments']);
                $place['mark_five_count'] = 0;
                $place['mark_average'] = 0;

                foreach($place['comments'] as $comment) {
                    $place['mark_average'] += $comment['mark'];
                    if($comment['mark'] == 5) {
                        $place['mark_five_count']++;
                    }
                }

                $place['mark_average'] /= $place['comments_count'];
            })
            ->all();

        print_r($result);
    }

    /**
     * Получаем города из списка ID с адресами
     * @throws QueryRelationManagerException
     */
    public function actionCity()
    {
        $cityIds = City::find()->limit(2)->offset(1)->select('id')->column();

        $result = QueryRelationManager::select(City::class, 'c')
            ->withMultiple('addresses', Address::class, 'a', 'c', ['city_id' => 'id'])
            ->filter(function(Query $q) use ($cityIds) {
                $q->andWhere(['c.id' => $cityIds])->orderBy(['a.id' => SORT_ASC]);
            })
            ->all();

        print_r($result);
    }
}
