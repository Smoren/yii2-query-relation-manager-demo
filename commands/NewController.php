<?php


namespace app\commands;


use app\models\Address;
use app\models\City;
use app\models\Comment;
use app\models\Place;
use app\qrm\ActiveRecord\QueryRelationDataProvider;
use app\qrm\ActiveRecord\QueryRelationManager;
use app\qrm\Base\QueryRelationManagerException;
use Yii;
use yii\console\Controller;
use yii\db\Query;

class NewController extends Controller
{
    /**
     * @throws \app\qrm\Base\QueryRelationManagerException
     */
    public function actionAddress()
    {
        $result = QueryRelationManager::select(Address::class, 'a')
            ->withSingle('city', City::class, 'c', 'a', ['id' => 'city_id'])
            ->withMultiple('places', Place::class, 'p', 'a', ['address_id' => 'id'])
            ->withMultiple('comments', Comment::class, 'cm', 'p', ['place_id' => 'id'])
            ->filter(function(Query $q) {
                $q->andWhere(['c.id' => 1]);
            })
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
     * @throws \app\qrm\Base\QueryRelationManagerException
     */
    public function actionProvider()
    {
        $qrm = QueryRelationManager::select(City::class, 'c')
            ->withMultiple('addresses', Address::class, 'a', 'c', ['city_id' => 'id']);

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
    /**
     * @throws \app\qrm\Base\QueryRelationManagerException
     */
    public function actionProviderAddr()
    {
        $qrm = QueryRelationManager::select(Address::class, 'a')
            ->withSingle('city', City::class, 'c', 'a', ['id' => 'city_id'])
            ->withMultiple('places', Place::class, 'p', 'a', ['address_id' => 'id'])
            ->withMultiple('comments', Comment::class, 'cm', 'p', ['place_id' => 'id']);

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

    /**
     * Выбираем адреса с городом, местами и комментариями о местах
     * @throws QueryRelationManagerException
     */
    public function actionPdo()
    {
        \app\qrm\Pdo\QueryWrapper::setDbConfig(Yii::$app->db->dsn, Yii::$app->db->username, Yii::$app->db->password);

        $result = \app\qrm\Pdo\QueryRelationManager::select('address', 'a')
            ->withSingle('city', 'city', 'c', 'a', ['id' => 'city_id'])
            ->withMultiple('places', 'place', 'p', 'a', ['address_id' => 'id'])
            ->withMultiple('comments', 'comment', 'cm', 'p', ['place_id' => 'id'])
            ->all();

        print_r($result);
    }
}