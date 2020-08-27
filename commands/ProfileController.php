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
use Smoren\Yii2\QueryRelationManager\ActiveRecord\QueryRelationManager;
use Smoren\Yii2\QueryRelationManager\Base\QueryRelationManagerException;
use yii\console\Controller;

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
        $count = 2000;

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

    protected function log(string $who, float $time)
    {
        echo "{$who}:\t{$time}\n";
    }
}
