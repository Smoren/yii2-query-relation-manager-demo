<?php


namespace app\commands;


use Smoren\Yii2\QueryRelationManager\Base\QueryRelationManagerException;
use Smoren\Yii2\QueryRelationManager\Pdo\QueryRelationManager;
use Smoren\Yii2\QueryRelationManager\Pdo\QueryWrapper;
use Yii;
use yii\console\Controller;

/**
 * Контроллер для демонстрации работы QueryRelationManager с PDO вместо ORM
 * @package app\commands
 * @author Smoren <ofigate@gmail.com>
 */
class PdoController extends Controller
{
    /**
     * Выбираем адреса с городом, местами и комментариями о местах
     * Используем PDO вместо ORM
     * @throws QueryRelationManagerException
     */
    public function actionAddress()
    {
        QueryWrapper::setDbConfig(Yii::$app->db->dsn, Yii::$app->db->username, Yii::$app->db->password);

        $result = QueryRelationManager::select('address', 'a')
            ->withSingle('city', 'city', 'c', 'a', ['id' => 'city_id'])
            ->withMultiple('places', 'place', 'p', 'a', ['address_id' => 'id'])
            ->withMultiple('comments', 'comment', 'cm', 'p', ['place_id' => 'id'])
            ->all();

        print_r($result);
    }
}