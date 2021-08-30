<?php namespace Dynamedia\Posts\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Dynamedia\Posts\Classes\Helpers\ThemeHelper;

/**
 * Category Translations Backend Controller
 */
class CategoryTranslations extends Controller
{
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class,
        \Backend\Behaviors\RelationController::class,
    ];

    /**
     * @var string formConfig file
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var string listConfig file
     */
    public $listConfig = 'config_list.yaml';

    /**
     * @var string Configuration file for the `RelationController` behavior.
     */
    public $relationConfig = 'config_relation.yaml';

    /**
     * __construct the controller
     */
    public function __construct()
    {
        parent::__construct();

        $this->addCss(ThemeHelper::getBackendCss(), "1.0.0");
        BackendMenu::setContext('Dynamedia.Posts', 'posts', 'categorytranslations');
    }
}
