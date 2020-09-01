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
use Smoren\Yii2\QueryRelationManager\Yii2\QueryRelationDataProvider;
use Smoren\Yii2\QueryRelationManager\Yii2\QueryRelationManager;
use Smoren\Yii2\QueryRelationManager\Base\QueryRelationManagerException;
use Yii;
use yii\console\Controller;
use yii\data\ActiveDataProvider;
use yii\db\Exception;

/**
 * Контроллер для профилирования QueryRelationManager
 * @package app\commands
 * @author Smoren <ofigate@gmail.com>
 */
class ProfileController extends Controller
{
    /**
     * Выбираем адреса с городом, местами и комментариями о местах
     * Сравниваем скорость работы ActiveQuery::joinWith и QueryRelationManager
     * @throws QueryRelationManagerException
     * @throws Exception
     */
    public function actionAddress()
    {
        $this->genAddresses();

        $ph = ProfilerHelper::start();
        Address::find()
            ->joinWith('city')
            ->joinWith('places')
            ->joinWith('places.comments')
            ->asArray()
            ->all();
        $this->log('ActiveRecord joinWith', $ph->getTimeSpent());

        $ph = ProfilerHelper::start();
        QueryRelationManager::select(Address::class, 'a')
            ->withSingle('city', City::class, 'c', 'a', ['id' => 'city_id'])
            ->withMultiple('places', Place::class, 'p', 'a', ['address_id' => 'id'])
            ->withMultiple('comments', Comment::class, 'cm', 'p', ['place_id' => 'id'])
            ->all();
        $this->log('QueryRelationManager', $ph->getTimeSpent());

        $this->delAddresses();
    }

    /**
     * Выбираем адреса с городом, местами и комментариями о местах, используем DataProvider для постраничной навигации
     * Сравниваем скорость работы ActiveQuery::joinWith и QueryRelationManager
     * @throws QueryRelationManagerException
     * @throws Exception
     */
    public function actionProvider()
    {
        $this->genAddresses();

        $pageSize = 100;
        echo "page size: {$pageSize}\n";

        $ph = ProfilerHelper::start();
        $q = Address::find()
            ->joinWith('city')
            ->joinWith('places')
            ->joinWith('places.comments')
            ->asArray();

        $dataProvider = new ActiveDataProvider([
            'query' => $q,
            'pagination' => [
                'pageSize' => $pageSize,
                'page' => 0,
            ],
        ]);
        $totalCount = $dataProvider->getTotalCount();

        $rows = $dataProvider->getModels();
        $this->log('ActiveRecord joinWith', $ph->getTimeSpent(), count($rows), $totalCount);

        $ph = ProfilerHelper::start();
        $qrm = QueryRelationManager::select(Address::class, 'a')
            ->withSingle('city', City::class, 'c', 'a', ['id' => 'city_id'])
            ->withMultiple('places', Place::class, 'p', 'a', ['address_id' => 'id'])
            ->withMultiple('comments', Comment::class, 'cm', 'p', ['place_id' => 'id']);

        $dataProvider = new QueryRelationDataProvider([
            'queryRelationManager' => $qrm,
            'pagination' => [
                'pageSize' => $pageSize,
                'page' => 0,
            ],
        ]);
        $rows = $dataProvider->getModels();
        $totalCount = $dataProvider->getTotalCount();

        $this->log('QRM with totalCount', $ph->getTimeSpent(), count($rows), $totalCount);

        $ph = ProfilerHelper::start();
        $qrm = QueryRelationManager::select(Address::class, 'a')
            ->withSingle('city', City::class, 'c', 'a', ['id' => 'city_id'])
            ->withMultiple('places', Place::class, 'p', 'a', ['address_id' => 'id'])
            ->withMultiple('comments', Comment::class, 'cm', 'p', ['place_id' => 'id']);

        $dataProvider = new QueryRelationDataProvider([
            'queryRelationManager' => $qrm,
            'withoutTotalCount' => true,
            'pagination' => [
                'pageSize' => $pageSize,
                'page' => 0,
            ],
        ]);
        $rows = $dataProvider->getModels();
        $totalCount = $dataProvider->getTotalCount();

        $this->log('QRM without totalCount', $ph->getTimeSpent(), count($rows), $totalCount);

        $this->delAddresses();
    }

    /**
     * Генерирует миллион адресов
     * @throws Exception
     */
    protected function genAddresses()
    {
        echo "generating addresses...\n";
        for($j=0; $j<100; $j++) {
            $testBuf = [];
            for($i=0; $i<10000; $i++) {
                $testBuf[] = [1, uniqid()];
            }
            Yii::$app->db->createCommand()->batchInsert('address', ['city_id', 'name'], $testBuf)->execute();
        }
        echo "ok!\n";
    }

    /**
     * Удаляет сгенерированные адреса
     * @throws Exception
     */
    protected function delAddresses()
    {
        echo "removing addresses...\n";
        Yii::$app->db->createCommand('DELETE FROM address where id > 4')->execute();
        echo "ok!\n";
    }

    /**
     * Выводит данные профилирования в консоль
     * @param string $who предмет замера
     * @param float $time время работы
     * @param int|null $foundCount найдено записей
     * @param int|null $totalCount запрошено записей
     */
    protected function log(string $who, float $time, ?int $foundCount = null, ?int $totalCount = null)
    {
        $time = round($time, 4);
        echo "{$who}:\t{$time}";
        if($foundCount !== null) {
            echo " | found: {$foundCount}";
        }
        if($foundCount !== null) {
            echo " | total count: {$totalCount}";
        }
        echo "\n";
    }
}
