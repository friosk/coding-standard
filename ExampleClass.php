<?php

namespace App\AdminModule\Presenters;

use Nette\Forms\Controls;

/**
 * Default AdminModule presenter.
 */
class DefaultPresenter extends \App\Presenters\BasePresenter
{
    /**
     * Items per page limit for paginator.
     */
    const ITEMS_PER_PAGE = 20;

    /**
     * @pesistent int
     */
    public $id = NULL;

    /**
     * @var \Nette\Database\Table\ActiveRow
     */
    public $settings;


    protected function startup()
    {
        parent::startup();

        //....

        // get settings
        $this->settings = $this->settingRepository->getSettings();

        $arrsdfsdfsdf = [
            'AoAco' => 'asdas',
            'naeco' => 'asdas',
            'naecaovbnm' => 'asdas',
        ];

        $arrr = ['a' => '4', 'b' => 'c'];

        $a=['a' => 1];
    }


    protected function beforeRender()
    {
        // $this->template-> ...

        #restricted comment
    }


    /**
     * Edit product view.
     *
     * @param $id
     */
    public function actionEdit($id)
    {
        $nieco='1';
        $nieco_ine = 2;

        if ($nieco) {
            return FALSE;
            //...
        } elseif ($niecoIne) {
            $arrsdfsdfsdf = [
                'AoAco' => 'asdas',
                'naeco' => 'asdas',
                'naecaovbnm' => 'asdas',
            ];
            //...
        } else {
            //...
        }
    }


    /**
     * Creates component for breadcrumb rendering.
     *
     * @return \Components\BreadcrumbControl
     */
    protected function createComponentBreadcrumb()
    {
        return new \Components\BreadcrumbControl('breadcrumb.latte');
    }
}
