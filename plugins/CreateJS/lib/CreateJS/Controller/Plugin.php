<?php

include_once("simple_html_dom.php"); // already used by pimcore ..

/**
 * based on @see Pimcore_Controller_Plugin_CssMinify
 */
class CreateJS_Controller_Plugin extends Zend_Controller_Plugin_Abstract {

    /**
     * @var array
     */
    private $namespaces = array();


    /**
     * analyse and prepare the html by inserting content
     */
    public function dispatchLoopShutdown() {

        if (!Pimcore_Tool::isHtmlResponse($this->getResponse())) {
            return;
        }

        $view = Zend_Controller_Action_HelperBroker::getExistingHelper(
            'ViewRenderer'
        )->view;

        $body = $this->getResponse()->getBody();
        $domElement = str_get_html($body);

        if ($view->editmode) {

            $this->injectEditModeCreateJSResources($domElement);
        }

        $this->namespaces = $this->getNamespaceAliases($domElement);

        foreach ($domElement->find('[about]') as $element) {

            if ($element->about == '@document') {

                $element = $this->updateDocument($element);
            }

            // "handle" objects ...
            if (
                preg_match(
                    '/\/object\/[a-zA-Z]+\/([0-9]+)/',
                    $element->about,
                    $match
                )
            ) {

                $element = $this->updateObject($match[1], $element);
            }
        }

        $body = $domElement->save();
        $this->getResponse()->setBody($body);

    }

    /**
     *
     */
    private function injectEditModeCreateJSResources($domElement)
    {

        $headTag = $domElement->find('head', 0);

        $editModeResources = <<<EOF
        <script src="/plugins/CreateJS/static/js/jquery-1.7.1.min.js"></script>
        <script src="/plugins/CreateJS/static/js/jquery-ui-1.8.18.custom.min.js"></script>
        <script src="/plugins/CreateJS/static/js/modernizr.custom.80485.js"></script>
        <script src="/plugins/CreateJS/static/js/underscore-min.js"></script>
        <script src="/plugins/CreateJS/static/js/backbone-min.js"></script>
        <script src="/plugins/CreateJS/static/js/vie-min.js"></script>
        <script src="/plugins/CreateJS/static/js/jquery.rdfquery.min.js"></script>
        <script src="/plugins/CreateJS/static/js/annotate-min.js"></script>
        <script src="/plugins/CreateJS/static/js/create.js"></script>
        <script src="/plugins/CreateJS/static/js/hallo-min.js"></script>
        <script src="/plugins/CreateJS/static/js/rangy-core-1.2.3.js"></script>
        <script src="/plugins/CreateJS/static/js/create-hallo.js"></script>

        <link rel="stylesheet" href="/plugins/CreateJS/static/css/create-ui.css" />
        <link rel="stylesheet" href="/plugins/CreateJS/static/css/midgardnotif.css" />
        <link rel="stylesheet" href="/plugins/CreateJS/static/css/font-awesome.css" />
        <link rel="stylesheet" href="/plugins/CreateJS/static/css/font-awesome-ie7.css" />
EOF;

        $headTag->innertext .= $editModeResources;

        return $domElement;
    }

    /**
     * @param $domElement
     * @return array
     */
    private function getNamespaceAliases($domElement) {

        $namespaces = array();

        $bodyTag = $domElement->find('body', 0);

        foreach ($bodyTag->attr as $attrKey => $attrValue) {

            if (preg_match('/^xmlns:(.+)$/', $attrKey, $match)) {

                $this->namespaces[$match[1]] = $attrValue;
            }
        }

        return $namespaces;
    }

    /**
     * inject document content into the RDFa tags
     *
     * @param $domElement
     * @return mixed the modified dom element
     */
    private function updateDocument($domElement)
    {
        $view = Zend_Controller_Action_HelperBroker::getExistingHelper(
            'ViewRenderer'
        )->view;

        $document =  $view->document;

        // replace @document about attribute w/ current document
        $domElement->about = 'http://pimcore/document/' .$document->getId();

        foreach ($domElement->find('[property]') as $element) {

            $propertyName = $element->property;

            list($namespaceKey, $propertyKey) = explode(':', $propertyName);
            if (array_key_exists($namespaceKey, $this->namespaces)) {
                $propertyName = $this->namespaces[$namespaceKey] . $propertyKey;
            }

            $innerText = '[['.$propertyName.']]'; // default contents

            $documentElement = $document->getElement('<'.$propertyName.'>');

            if (is_object($documentElement)) {
                $innerText = $documentElement->getValue();
            }
            $element->innertext = $innerText;
        }
        return $domElement;
    }

    /**
     * inject object content into the RDFa tags
     *
     * @param $id int the pimcore id of the object
     * @param $domElement
     * @return mixed the modified dom element
     */
    private function updateObject($id, $domElement)
    {

        $obj = Object_Abstract::getById($id);

        foreach ($domElement->find('[property]') as $element) {

            $propertyName = $element->property;

            list($namespaceKey, $propertyKey) = explode(':', $propertyName);

            if (array_key_exists($namespaceKey, $this->namespaces)) {
                $propertyName = $this->namespaces[$namespaceKey] . $propertyKey;
            }

            $innerText = '(('.$propertyName.'))'; // default contents

            // just strip out the last part to use as a property key ...
            if (
                preg_match(
                    '/\/object\/[a-zA-Z]+\/([a-zA-Z]+)/',
                    $propertyName,
                    $match
                )
            ) {
                $innerText = $obj->{$match[1]};
            }

            $element->innertext = $innerText;
        }
        return $obj;
    }

}
