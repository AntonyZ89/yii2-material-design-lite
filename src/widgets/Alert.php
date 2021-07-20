<?php

namespace antonyz89\mdl\widgets;

use antonyz89\mdl\Html;
use Yii;
use yii\bootstrap\Alert as BootstrapAlert;

class Alert extends \yii\bootstrap\Widget
{
    public $alertTypes = [
        'error' => [
            'class' => 'alert-danger',
            'icon' => '<i class="material-icons"></i>',
        ],
        'danger' => [
            'class' => 'alert-danger',
            'icon' => '<i class="material-icons"></i>',
        ],
        'success' => [
            'class' => 'alert-success',
            'icon' => '<i class="material-icons"></i>',
        ],
        'info' => [
            'class' => 'alert-info',
            'icon' => '<i class="material-icons"></i>',
        ],
        'warning' => [
            'class' => 'alert-warning',
            'icon' => '<i class="material-icons"></i>',
        ],
    ];

    public $closeButton = [];

    public $isAjaxRemoveFlash = true;

    public function init()
    {
        parent::init();

        $session = Yii::$app->session;
        $flashes = $session->allFlashes;
        $appendCss = $this->options['class'] ?? null;

        foreach ($flashes as $type => $data) {
            if (isset($this->alertTypes[$type])) {
                $data = (array) $data;

                foreach ($data as $message) {
                    $this->options['class'] = $this->alertTypes[$type]['class'] . ' ' . $appendCss;
                    $this->options['id'] = $this->getId() . '-' . $type;

                    echo Html::cell(
                        BootstrapAlert::widget([
                            'body' => $this->alertTypes[$type]['icon'] . $message,
                            'closeButton' => $this->closeButton,
                            'options' => $this->options,
                        ])
                    );
                }

                if ($this->isAjaxRemoveFlash && !Yii::$app->request->isAjax) {
                    $session->removeFlash($type);
                }
            }
        }
    }
}
