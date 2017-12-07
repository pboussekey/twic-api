<?php
/**
 * TheStudnet (http://thestudnet.com).
 *
 * Library
 */
namespace Application\Service;

use Dal\Service\AbstractService;
use Zend\Db\Sql\Predicate\IsNull;
use Box\Model\Document as ModelDocument;
use Application\Model\Role as ModelRole;
use JRpc\Json\Server\Exception\JrpcException;

/**
 * Class Library.
 */
class Library extends AbstractService
{

    /**
     * Add File in library
     *
     * @invokable
     *
     * @param  string $name
     * @param  string $link
     * @param  string $token
     * @param  string $type
     * @param  int    $folder_id
     * @param  string $text
     * @throws \Exception
     * @return \Application\Model\Library
     */
    public function add($name, $link = null, $token = null, $type = null, $folder_id = null, $global = null, $folder_name = null, $text = null)
    {
        $user_id = $this->getServiceUser()->getIdentity()['id'];

        if (null !== $folder_name && null === $folder_id) {
            $m_library = $this->getModel()
                ->setDeletedDate(new IsNull())
                ->setName($folder_name);

            if (null === $global || false === $global) {
                $m_library->setOwnerId($user_id);
            } else {
                $m_library->setGlobal(true);
            }
            $res_library = $this->getMapper()->select($m_library);
            if ($res_library->count() > 0) {
                $folder_id = $res_library->current()->getId();
            }
        }

        $box_id = null;
        if ((null !== $link || null !== $token) && null !== $type) {
            $urldms = $this->container->get('config')['app-conf']['urldms'];
            $u = (null !== $link) ? $link : $urldms . $token;
            $m_box = $this->getServiceBox()->addFile($u, $type);
            if ($m_box instanceof ModelDocument) {
                $box_id = $m_box->getId();
            }
        }
        if (null !== $text && null === $type) {
            $type = "text";
        }
        if ($global === true && $folder_id !== null) {
            $global = false;
        }

        $m_library = $this->getModel()
            ->setName($name)
            ->setLink((($link === '')?new IsNull('link'):$link))
            ->setText((($text === '')?new IsNull('text'):$text))
            ->setToken((($token === '')?new IsNull('token'):$token))
            ->setBoxId($box_id)
            ->setGlobal($global)
            ->setFolderId($folder_id)
            ->setType($type)
            ->setOwnerId($user_id)
            ->setCreatedDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'));

        if ($this->getMapper()->insert($m_library) < 0) {
            throw new \Exception('Error insert file');
        }

        $id = (int)$this->getMapper()->getLastInsertValue();

        return $this->get($id);
    }

    /**
     * Add library
     *
     * @param  array $data
     * @return \Application\Model\Library
     */
    public function _add($data)
    {
        $name = ((isset($data['name'])) ? $data['name'] : null);
        $link = ((isset($data['link'])) ? $data['link'] : null);
        $token = ((isset($data['token'])) ? $data['token'] : null);
        $type = ((isset($data['type'])) ? $data['type'] : null);
        $folder_id = ((isset($data['folder_id'])) ? $data['folder_id'] : null);
        $text = ((isset($data['text'])) ? $data['text'] : null);

        return $this->add($name, $link, $token, $type, $folder_id, null, null, $text);
    }

    /**
     * Get List Library
     *
     * @invokable
     *
     * @param  array   $filter
     * @param  int     $folder_id
     * @param  bool    $global
     * @param  string  $folder_name
     * @param  int     $user_id
     * @param  int     $page_id
     *
     * @return array
     */
    public function getList($filter = null, $folder_id = null, $global = null, $folder_name = null, $user_id = null, $page_id = null)
    {
        $identity = $this->getServiceUser()->getIdentity();
        if (null !== $user_id) {
            $global = true;
        } else {
            $user_id = $identity['id'];
        }

        // on récupere le folder selectionné
        if (null !== $folder_name && null === $folder_id) {
            $m_library = $this->getModel()
                ->setDeletedDate(new IsNull())
                ->setName($folder_name);

            if (null === $global || false === $global) {
                $m_library->setOwnerId($user_id);
            } else {
                $m_library->setGlobal(true);
            }
            $res_library = $this->getMapper()->select($m_library);
            if ($res_library->count() > 0) {
                $folder_id = $res_library->current()->getId();
            }
        }

        $mapper = (null !== $filter) ?
            $this->getMapper()->usePaginator($filter) :
            $this->getMapper();

        $is_sadmin = (in_array(ModelRole::ROLE_ADMIN_STR, $identity['roles']));
        $res_library = $mapper->getList($folder_id, $user_id, $page_id, $is_sadmin);

        $ar = [
            'count' => $mapper->count(),
            'documents' => $res_library,
            'folder' => null,
            'parent' => null
        ];
        // If root folder: returns only documents
        if ($folder_id) {
            // Requested document / folder
            $folder = $this->getMapper()->select($this->getModel()->setId($folder_id))->current();
            // Parent folder
            $parent = ($folder && is_numeric($folder->getFolderId())) ?
                $this->getMapper()->select($this->getModel()->setId($folder->getFolderId()))->current() : null;

            $ar['folder'] = $folder;
            $ar['parent'] = $parent;
        }

        return $ar;
    }

    /**
     * Update Library
     *
     * @invokable
     *
     * @param  int    $id
     * @param  string $name
     * @param  string $link
     * @param  string $token
     * @param  int    $folder_id
     * @param  string $type
     * @return \Application\Model\Library
     */
    public function update($id, $name = null, $link = null, $token = null, $folder_id = null, $type = null, $text = null)
    {
        if ($folder_id === $id) {
            return 0;
        }


        $box_id = null;
        if ((null !== $link || null !== $token) && null !== $type) {
            $urldms = $this->container->get('config')['app-conf']['urldms'];
            $u = (null !== $link) ? $link : $urldms . $token;
            $m_box = $this->getServiceBox()->addFile($u, $type);
            if ($m_box instanceof ModelDocument) {
                $box_id = $m_box->getId();
            }
        }

        $m_library = $this->getModel()
            ->setId($id)
            ->setName($name)
            ->setLink($link)
            ->setToken($token)
            ->setType($type)
            ->setBoxId($box_id)
            ->setText($text)
            ->setFolderId(($folder_id === 0) ? new IsNull() : $folder_id)
            ->setUpdatedDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'));

        $this->getMapper()->update($m_library);

        return $this->get($id);
    }

    /**
     * Get List Library By Post id
     * Appeler par pos.get
     *
     * @param  int $post_id
     * @return \Dal\Db\ResultSet\ResultSet
     */
    public function getListByPost($post_id)
    {
        return $this->getMapper()->getListByPost($post_id);
    }

    /**
     * delete Library
     *
     * @invokable
     *
     * @param  int $id
     * @return int
     */
    public function delete($id)
    {
        $user_id = $this->getServiceUser()->getIdentity()['id'];
        $m_library = $this->getModel()->setDeletedDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'));

        return $this->getMapper()->update($m_library, ['owner_id' => $user_id, 'id' => $id]);
    }

    /**
     * Get Library
     *
     * @invokable
     *
     * @param  int|array $id
     * @return \Application\Model\Library|\Dal\Db\ResultSet\ResultSet
     */
    public function get($id)
    {
        $res_library = $this->getMapper()->select($this->getModel()->setId($id));

        return (is_array($id)) ? $res_library->toArray(['id']) : $res_library->current();
    }

    /**
     * Get Box Session
     *
     * @invokable
     *
     * @param  int    $id
     * @param  string $box_id
     * @throws \Exception
     * @throws JrpcException
     * @return void|\Box\Model\Session
     */
    public function getSession($id = null, $box_id = null)
    {
        if (null === $id && null === $box_id) {
            return;
        }

        if (null !== $id) {
            $res_library = $this->getMapper()->select(
                $this->getModel()
                    ->setId($id)
            );

            if ($res_library->count() <= 0) {
                throw new \Exception();
            }
            $m_library = $res_library->current();
            $box_id = $m_library->getBoxId();
            if (empty($box_id)) {
                throw new JrpcException('No Box Id', 123456);
            }
        }

        $session = null;
        try {
            $session = $this->getServiceBox()->createSession($box_id);
        } catch (\Exception $e) {
            throw new JrpcException($e->getMessage(), $e->getCode());
        }

        return $session;
    }
    
    
    /**
     * Upload a file by url and return dms token
     *
     * @param  string $url
     * @return string
     */
    public function upload($url, $name)
    {
        
        
        $Client = new \Zend\Http\Client();
        $Client->setUri(str_replace('/data/', '/save/', $this->container->get('config')['app-conf']['urldms']));
        $Client->setMethod('POST');
        $Client->setFileUpload($name, "data" , file_get_contents($url) );
        $r = $Client->send();
        return json_decode($r->getBody(), 1)['data'];
        
    }

    /**
     * Get Service User.
     *
     * @return \Application\Service\User
     */
    private function getServiceUser()
    {
        return $this->container->get('app_service_user');
    }

    /**
     * Get Service Box Api
     *
     * @return \Box\Service\Api
     */
    private function getServiceBox()
    {
        return $this->container->get('box.service');
    }
}
