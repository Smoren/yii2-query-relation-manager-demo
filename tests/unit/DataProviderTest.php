<?php

namespace app\tests\unit;


use app\models\Address;
use app\models\City;
use Smoren\QueryRelationManager\Base\QueryRelationManagerException;
use Smoren\QueryRelationManager\Yii2\QueryRelationDataProvider;
use Smoren\QueryRelationManager\Yii2\QueryRelationManager;
use Yii;

class DataProviderTest extends \Codeception\Test\Unit
{
    /**
     * @throws QueryRelationManagerException
     */
    public function testCity()
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

        $result = $dataProvider->getModels();
        expect_that($this->compareResultWithCorrectMap($result, [
            1 => [1, 2],
            2 => [3, 4],
        ]));

        $dataProvider = new QueryRelationDataProvider([
            'queryRelationManager' => $qrm,
            'pagination' => [
                'pageSize' => 2,
                'page' => 1,
            ],
        ]);
        $result = $dataProvider->getModels();
        expect_that($this->compareResultWithCorrectMap($result, [
            3 => [],
            4 => [],
        ]));

        $dataProvider = new QueryRelationDataProvider([
            'queryRelationManager' => $qrm,
            'pagination' => [
                'pageSize' => 2,
                'page' => 2,
            ],
        ]);
        $result = $dataProvider->getModels();
        expect_that($this->compareResultWithCorrectMap($result, [
            5 => [],
        ]));

        $dataProvider = new QueryRelationDataProvider([
            'queryRelationManager' => $qrm,
            'pagination' => [
                'pageSize' => 2,
                'page' => 3,
            ],
        ]);
        $result = $dataProvider->getModels();
        expect_that($this->compareResultWithCorrectMap($result, []));

        $dataProvider = new QueryRelationDataProvider([
            'queryRelationManager' => $qrm,
            'pagination' => [
                'pageSize' => 1,
                'page' => 0,
            ],
        ]);
        $result = $dataProvider->getModels();
        expect_that($this->compareResultWithCorrectMap($result, [
            1 => [1, 2],
        ]));

        $dataProvider = new QueryRelationDataProvider([
            'queryRelationManager' => $qrm,
            'pagination' => [
                'pageSize' => 5,
                'page' => 0,
            ],
        ]);
        $result = $dataProvider->getModels();
        expect_that($this->compareResultWithCorrectMap($result, [
            1 => [1, 2],
            2 => [3, 4],
            3 => [],
            4 => [],
            5 => [],
        ]));

        $dataProvider = new QueryRelationDataProvider([
            'queryRelationManager' => $qrm,
            'pagination' => [
                'pageSize' => 1000,
                'page' => 0,
            ],
        ]);
        $result = $dataProvider->getModels();
        expect_that($this->compareResultWithCorrectMap($result, [
            1 => [1, 2],
            2 => [3, 4],
            3 => [],
            4 => [],
            5 => [],
        ]));

        $dataProvider = new QueryRelationDataProvider([
            'queryRelationManager' => $qrm,
            'pagination' => [
                'pageSize' => 1000,
                'page' => 1,
            ],
        ]);
        $result = $dataProvider->getModels();
        expect_that($this->compareResultWithCorrectMap($result, []));
    }

    /**
     * @param array $result
     * @param array $correctMap
     * @return bool
     */
    protected function compareResultWithCorrectMap(array $result, array $correctMap)
    {
        $resultMap = [];
        foreach($result as $city) {
            $resultMap[$city['id']] = [];
            foreach($city['addresses'] as $address) {
                $resultMap[$city['id']][] = $address['id'];
            }
            sort($resultMap[$city['id']]);
        }
        ksort($resultMap);

        return $resultMap == $correctMap;
    }
}