<?php

/**
 * Zend Framework (http://framework.zend.com/).
 *
 * @link http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 *
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use JRpc\Json\Server\Exception\JrpcException;

/**
 * Controller Index
 */
class IndexController extends AbstractActionController
{
    const ITEM_STARTING = 'item.starting';
    /**
     * Index
     *
     * {@inheritDoc}
     *
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Location', 'doc/index.html');
        $response->setStatusCode(302);

        return $response;
    }

    /**
     * Check Status
     *
     * @return \Zend\View\Model\JsonModel
     */
    public function statusChangeAction()
    {
        $ret = $this->videoArchive()->checkStatus($this->getRequest()->getContent());

        return new JsonModel(['code'=>$ret]);
    }

      /**
       * Check Status
       *
       * @return \Zend\View\Model\JsonModel
       */
    public function notifyAction()
    {
        $authorization = $this->conf()->getAll()['node']['authorization'];
        $request = $this->getRequest();
        
        if($request->getHeaders()->get('x-auth-token') !== false && $authorization === $request->getHeader('x-auth-token')->getFieldValue()) {
            $notifs = json_decode($this->getRequest()->getContent(), true);
            foreach($notifs as $notif){
                switch($notif['type']){
                case self::ITEM_STARTING :
                    $ret = $this->item()->starting($notif['data']['id']);
                    break;
                }
            }

            return new JsonModel(['code'=>$ret]);
        }
        else{
            throw new JrpcException('No authorization: notify', - 32029);
        }

    }
    
    /**
     * Check Status
     *
     * @return \Zend\View\Model\JsonModel
     */
    public function uptboxidAction()
    {
        $authorization = $this->conf()->getAll()['node']['authorization'];
        $request = $this->getRequest();
        $ret = -1;
        $params = -1;
        $content = -1;
        if ($request->getHeaders()->get('x-auth-token') !== false && $authorization === $request->getHeader('x-auth-token')->getFieldValue()) {
            $content = $this->getRequest()->getContent();
            $params = json_decode($content, true);
            if($params['id'] && is_numeric($params['id']) && $params['box_id'] && is_numeric($params['box_id'])) {
                $ret = $this->library()->updateBoxId($params['id'], $params['box_id']);
            }
        }       
        else {  
            throw new JrpcException('No authorization: uptboxid', - 32029);
        }  
        
        return new JsonModel(['code'=>$ret, 'params' => $params, 'content' => $content]);
    }

}
