<?php

class CreateJS_AdminController extends Pimcore_Controller_Action_Admin
{

    /**
     * @param $id int
     * @param $model array a hash containing the data from the CreateJS
     */
    private function updateDocument($id, $model) {

        /** @var $page Document_Page */
        $page = Document::getById((int)$id);

        foreach ($model as $elementKey => $elementValue) {

            // skip links
            if (substr($elementKey, 0, 1) == '@') {
                continue;
            }

            $page->setRawElement($elementKey, 'input', $elementValue);
        }

        $page->save();
    }

    /**
     * @param $className string unused ...
     * @param $id int
     * @param $model array a hash containing data from the CreateJS
     */
    private function updateObject($className, $id, $model) {

        $item = Object_Abstract::getById($id);

        foreach ($model as $elementKey => $elementValue) {

            // skip links
            if (substr($elementKey, 0, 1) == '@') {
                continue;
            }

            // just strip out the last string part to get a usable
            // key to store data under ..
            if (preg_match('/\/([a-zA-Z]+)>$/', $elementKey, $match)) {

                $item->{$match[1]} = $elementValue;
            }
            $item->save();
        }
    }


    /**
     * Perform a backbone sync for CreateJS
     * @return array
     * @throws Exception
     */
    public function syncAction()
    {
        try {

            $request = $this->getRequest();

            $model = $request->getParam('model', false);

            if (!is_array($model)) {

                throw new Exception('No valid model parameter found.');
            }

            if (!array_key_exists('@subject', $model)) {

                throw new Exception('No valid subject in model found.');
            }

            $subject = $model['@subject'];

            // @todo handle types "properly" and refactor this ... piece ...

            if (preg_match('/\/document\/([0-9]+)/', $subject, $match)) {
                $this->updateDocument($match[1], $model);
            } elseif (preg_match('/\/object\/([^\/]+)\/([0-9]+)/', $subject, $match)) {
                $this->updateObject($match[1], $match[2], $model);
            } else {

                throw new Exception('Unable to parse subject / subject unsupported.');
            }

            $result = array(
                'error' => null,
                'result' => array(
                    'success' => true,
                )
            );

        } catch (Exception $exception) {

            $result = array(
                'error' => array(
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                ),
                'result' => null
            );
        }

        return $this->_helper->json($result);
    }
}

