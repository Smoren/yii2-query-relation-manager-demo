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
use Smoren\Yii2\QueryRelationManager\QueryRelationDataProvider;
use Smoren\Yii2\QueryRelationManager\QueryRelationManager;
use Smoren\Yii2\QueryRelationManager\QueryRelationManagerException;
use Yii;
use yii\console\Controller;
use yii\db\Query;

/**
 * Контроллер для демонстрации работы QueryRelationManager
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
            ->withSingle('city', City::class, 'c', 'a', 'id', 'city_id')
            ->withMultiple('places', Place::class, 'p', 'a', 'address_id', 'id')
            ->withMultiple('comments', Comment::class, 'cm', 'p', 'place_id', 'id')
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
            ->withSingle('address', Address::class, 'a', 'p', 'id', 'address_id')
            ->withSingle('city', City::class, 'c', 'a', 'id', 'city_id')
            ->withMultiple('comments', Comment::class, 'cm', 'p', 'place_id', 'id',
                'inner', 'and cm.mark >= :mark', [':mark' => 3])
            ->modify('cm', function(array &$comment, array &$place) {
                if(!isset($place['comments_count'])) {
                    $place['comments_count'] = 0;
                }

                if(!isset($place['mark_five_count'])) {
                    $place['mark_five_count'] = 0;
                }

                if(!isset($place['mark_average'])) {
                    $place['mark_average'] = 0;
                }

                $place['comments_count']++;
                $place['mark_average'] += $comment['mark'];

                if($comment['mark'] == 5) {
                    $place['mark_five_count']++;
                }
            })
            ->modify('p', function(array &$place) {
                if(!isset($place['mark_average'])) {
                    $place['mark_average'] = 0;
                } else {
                    $place['mark_average'] /= $place['comments_count'];
                }
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
        // if you need pagination (do not use inner join!)
        $cityIds = City::find()->limit(2)->offset(1)->select('id')->column();

        $result = QueryRelationManager::select(City::class, 'c')
            ->withMultiple('addresses', Address::class, 'a', 'c', 'city_id', 'id')
            ->filter(function(Query $q) use ($cityIds) {
                $q->andWhere(['c.id' => $cityIds])->orderBy(['a.id' => SORT_ASC]);
            })
            ->all();

        print_r($result);
    }

    /**
     * @throws QueryRelationManagerException
     */
    public function actionProvider()
    {
        $qrm = QueryRelationManager::select(City::class, 'c')
            ->withMultiple('addresses', Address::class, 'a', 'c', 'city_id', 'id');

        $dataProvider = new QueryRelationDataProvider([
            'queryRelationManager' => $qrm,
            'db' => Yii::$app->db,
            'pagination' => [
                'pageSize' => 2,
                'page' => 0,
            ],
        ]);

        print_r($dataProvider->getModels());
    }
}
