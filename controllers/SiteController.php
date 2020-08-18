<?php

namespace app\controllers;

use yii\web\Controller;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Returns current timestamp.
     *
     * @return string
	 * @format json
     */
    public function actionTimestamp()
    {
        return date("h:i:s A", time());
    }
}
