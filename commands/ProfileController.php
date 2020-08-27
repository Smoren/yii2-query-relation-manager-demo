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
use app\helpers\ProfilerHelper;
use Smoren\Yii2\QueryRelationManager\ActiveRecord\QueryRelationDataProvider;
use Smoren\Yii2\QueryRelationManager\ActiveRecord\QueryRelationManager;
use Smoren\Yii2\QueryRelationManager\Base\QueryRelationManagerException;
use Yii;
use yii\console\Controller;
use yii\data\ActiveDataProvider;
use yii\db\Exception;

/**
 * Контроллер для демонстрации работы QueryRelationManager
 * @author Smoren <ofigate@gmail.com>
 */
class ProfileController extends Controller
{
    /**
     * Выбираем адреса с городом, местами и комментариями о местах
     * @throws QueryRelationManagerException
     */
    public function actionAddress()
    {
        $count = 1;

        $ph = ProfilerHelper::start();
        for($i=0; $i<$count; $i++) {
            Address::find()
                ->joinWith('city')
                ->joinWith('places')
                ->joinWith('places.comments')
                ->asArray()
                ->all();
        }
        $this->log('ActiveRecord joinWith', $ph->getTimeSpent());

        $ph = ProfilerHelper::start();
        for($i=0; $i<$count; $i++) {
            QueryRelationManager::select(Address::class, 'a')
                ->withSingle('city', City::class, 'c', 'a', 'id', 'city_id')
                ->withMultiple('places', Place::class, 'p', 'a', 'address_id', 'id')
                ->withMultiple('comments', Comment::class, 'cm', 'p', 'place_id', 'id')
                ->all();
        }
        $this->log('QueryRelationManager', $ph->getTimeSpent());
    }

    /**
     * Выбираем адреса с городом, местами и комментариями о местах
     * @throws QueryRelationManagerException
     */
    public function actionProvider()
    {
        $count = 1;

        $ph = ProfilerHelper::start();
        for($i=0; $i<$count; $i++) {
            $q = Address::find()
                ->joinWith('city')
                ->joinWith('places')
                ->joinWith('places.comments')
                ->asArray();

            $dataProvider = new ActiveDataProvider([
                'query' => $q,
                'pagination' => [
                    'pageSize' => 100,
                    'page' => 100,
                ],
            ]);
            $dataProvider->getModels();
        }
        $this->log('ActiveRecord joinWith', $ph->getTimeSpent());

        $ph = ProfilerHelper::start();
        for($i=0; $i<$count; $i++) {
            $qrm = QueryRelationManager::select(Address::class, 'a')
                ->withSingle('city', City::class, 'c', 'a', 'id', 'city_id')
                ->withMultiple('places', Place::class, 'p', 'a', 'address_id', 'id')
                ->withMultiple('comments', Comment::class, 'cm', 'p', 'place_id', 'id');

            $dataProvider = new QueryRelationDataProvider([
                'queryRelationManager' => $qrm,
                'pagination' => [
                    'pageSize' => 100,
                    'page' => 100,
                ],
            ]);
            $dataProvider->getModels();
        }

        $this->log('QueryRelationManager', $ph->getTimeSpent());
    }

    /**
     * @throws Exception
     */
    public function actionGen()
    {
        for($j=0; $j<100; $j++) {
            $testBuf = [];
            for($i=0; $i<10000; $i++) {
                $testBuf[] = [1, uniqid()];
            }
            Yii::$app->db->createCommand()->batchInsert('address', ['city_id', 'name'], $testBuf)->execute();
        }
    }

    /**
     * @throws Exception
     */
    public function actionDel()
    {
        Yii::$app->db->createCommand('DELETE FROM address where id > 4')->execute();
    }

    /**
     * @param string $who
     * @param float $time
     */
    protected function log(string $who, float $time)
    {
        echo "{$who}:\t{$time}\n";
    }
}
