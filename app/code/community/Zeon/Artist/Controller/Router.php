<?php
class Zeon_Artist_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    /**
     * Initialize Controller Router
     *
     * @param Varien_Event_Observer $observer
     */
    public function initControllerRouters($observer)
    {
        /* @var $front Mage_Core_Controller_Varien_Front */
        $front = $observer->getEvent()->getFront();

        $front->addRouter('artist', $this);
    }

    /**
     * Validate and Match Artist Page and modify request
     *
     * @param Zend_Controller_Request_Http $request
     * @return bool
     */
    public function match(Zend_Controller_Request_Http $request)
    {
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }
        $router = 'artist';
        $identifier = trim(str_replace('/artist/', '', $request->getPathInfo()), '/');

        $condition = new Varien_Object(
            array(
                'identifier' => $identifier,
                'continue'   => true
            )
        );
        Mage::dispatchEvent(
            'artist_controller_router_match_before', 
            array(
                'router'    => $this,
                'condition' => $condition
            )
        );
        $identifier = $condition->getIdentifier();

        if ($condition->getRedirectUrl()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect($condition->getRedirectUrl())
                ->sendResponse();
            $request->setDispatched(true);
            return true;
        }
        if (!$condition->getContinue()) {
            return false;
        }
        $artist = Mage::getModel('zeon_artist/artist');
        $artistId = $artist->checkIdentifier($identifier, Mage::app()->getStore()->getId());
        if (trim($identifier) && $artistId) {
            $request->setModuleName('artist')
                ->setControllerName('index')
                ->setActionName('view')
                ->setParam('artist_id', $artistId);
            $request->setAlias(
                Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                $router.'/'.$identifier
            );
            return true;
        }
        return false;
    }
}