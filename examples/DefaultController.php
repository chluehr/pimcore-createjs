<?php
/**
 * Code sample: DefaultController, default action
 */
class DefaultController extends Website_Controller_Action
{

    public function defaultAction()
    {
        $front = Zend_Controller_Front::getInstance();
        $front->unregisterPlugin(
            'Pimcore_Controller_Plugin_Frontend_Editmode'
        );
        $front->registerPlugin(
            new CreateJS_Controller_Plugin(), 1000
        );
    }
}
