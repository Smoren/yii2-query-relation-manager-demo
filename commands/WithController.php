<?php


namespace app\commands;


use app\models\Address;
use app\models\City;
use Smoren\Yii2\QueryRelationManager\Base\QueryRelationManagerException;
use yii\console\Controller;

/**
 * Контроллер для демонстрации работы QueryRelationManager с использованием упрощенного синтаксиса
 * @package app\commands
 * @author Smoren <ofigate@gmail.com>
 */
class WithController extends Controller
{
    /**
     * Выбираем города с адресами и местами
     * Используем упрощенный синтаксис with
     * Метод City::select() добавлен в класс с помощью трейта ActiveRecordTrait
     * @see \Smoren\Yii2\QueryRelationManager\Yii2\ActiveRecordTrait
     * @throws QueryRelationManagerException
     */
    public function actionCity()
    {
        $result = City::select('c')
            ->with('addresses', 'a')
            ->with('places', 'p', 'a')
            ->all();

        print_r($result);
    }

    /**
     * Выбираем адреса с городом, местами и комментариями, оценка которых не ниже трех
     * Используем упрощенный синтаксис with
     * Метод City::select() добавлен в класс с помощью трейта ActiveRecordTrait
     * @see \Smoren\Yii2\QueryRelationManager\Yii2\ActiveRecordTrait
     * @throws QueryRelationManagerException
     */
    public function actionAddress()
    {
        $result = Address::select('a')
            ->with('city', 'c')
            ->with('places', 'p')
            ->with(
                'comments', 'cm', 'p',
                'left', 'and cm.mark >= :mark', [':mark' => 3]
            )
            ->all();

        print_r($result);
    }
}