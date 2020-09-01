<?php


namespace app\commands;


use app\models\Address;
use app\models\City;
use Smoren\Yii2\QueryRelationManager\Yii2\QueryRelationDataProvider;
use Smoren\Yii2\QueryRelationManager\Yii2\QueryRelationManager;
use Smoren\Yii2\QueryRelationManager\Base\QueryRelationManagerException;
use Yii;
use yii\console\Controller;

/**
 * Контроллер для демонстрации работы QueryRelationManager с использованием DataProvider
 * @package app\commands
 * @author Smoren <ofigate@gmail.com>
 */
class ProviderController extends Controller
{
    /**
     * Выбираем города с адресами, используем DataProvider для постраничной навигации
     * @throws QueryRelationManagerException
     */
    public function actionCity()
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
}