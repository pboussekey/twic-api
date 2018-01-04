<?php

namespace ModuleTest\Api;

use Application\Model\Role as ModelRole;
use DateTime;
use DateTimeZone;
use JrpcMock\Json\Server\Server;
use LinkedIn\Model\People;
use Zend\Json\Json;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use function GuzzleHttp\json_encode;

abstract class AbstractService extends AbstractHttpControllerTestCase
{
    protected $traceError = true;

    public function setUp()
    {
        parent::setUp();
    }

    public function reset($keepPersistence = false)
    {
        parent::reset($keepPersistence);

        $this->setApplicationConfig(include __DIR__ . '/../../config/application.config.php');
        $serviceLocator = $this->getApplicationServiceLocator();
        $serviceLocator->setAllowOverride(true);
        $serviceLocator->setFactory(
            'json_server_mock',
            function ($container, $requestedName, $options) {
                return new Server($container, $container->get('config')['json-rpc-server']);
            }
        );
        $serviceLocator->setAlias('json_server', 'json_server_mock');
    }

    public function tearDown()
    {
        $this->deleteDirRec(__DIR__ . '/../../upload/');
    }

    public function deleteDirRec($path)
    {
        foreach (glob($path . "/*") as $filename) {
            (! is_dir($filename)) ? unlink($filename) : $this->deleteDirRec($filename);
        }
        if (is_dir($path)) {
            rmdir($path);
        }
    }

    public function jsonRpc($method, array $params, $hasToken = null)
    {
        $postJson = array('method' => $method,'id' => 1,'params' => $params);

        return $this->jsonRpcRequest($postJson, $hasToken);
    }

    public function jsonRpcRequest($request, $hasToken = null)
    {
        if ($hasToken) {
            $this->getRequest()
                ->getHeaders()
                ->addHeaderLine('Authorization', $hasToken);
        }
        $ret = null;
        $postJson = json_encode($request);
        file_put_contents(__DIR__ . '/../../_files/input.data', $postJson);
        $this->getRequest()->setMethod('POST');

        $this->dispatch('/api.json-rpc');
        $response = $this->getResponse()->getContent();

        if (is_string($response)) {
            exit($response);
        } elseif (is_array($response)) {
            foreach ($response as $r) {
                $ret[] = Json::decode($r, Json::TYPE_ARRAY);
            }
        } else {
            $ret = Json::decode($response, Json::TYPE_ARRAY);
        }

        return $ret;
    }

    /**
     * To test notifications' mails
     */
    public function clearMail()
    {
        return file_put_contents(__DIR__ . '/../../_files/mail.data', "");
    }

    /**
     * To test notifications' mails
     */
    public function getMail()
    {
        return file_get_contents(__DIR__ . '/../../_files/mail.data');
    }

    // /////////////////////////////////////////////////////////////////////////////
    // /////////////////////////////////////////////////////////////////////////////
    public function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d1 = DateTime::createFromFormat('Y-m-d\TH:i:s\Z', $date, new DateTimeZone('UTC'));
        $d2 = DateTime::createFromFormat('Y-m-d H:i:s', $date, new DateTimeZone('UTC'));

        $d = ($d1 != false) ? $d1 : (($d2 != false) ? $d2 : false);

        return $d && ($d->format('Y-m-d\TH:i:s\Z') == $date || $d->format('Y-m-d H:i:s') == $date);
    }

    public function validateToken($token)
    {
        return ((strpos($token, 'http://cdn.local') === 0) || (strlen($token) == 40));
    }

    public function printCreateTest($data, $name = "\$data")
    {
        if (is_object($data) || is_array($data)) {
            print("\$this->assertEquals(count(" . $name . ") , " . count($data) . "); \n");
            foreach ($data as $key => $val) {
                $fkey = is_object($data) ? "->" . $key : ((is_numeric($key)) ? "[" . $key . "]" : "['" . $key . "']");
                if (is_object($val) || is_array($val)) {
                    $this->printCreateTest($val, $name . $fkey);
                } else {
                    if (is_numeric($val)) {
                        print("\$this->assertEquals(" . $name . $fkey . " , " . $val . "); \n");
                    } elseif (is_bool($val)) {
                        print("\$this->assertEquals(" . $name . $fkey . " , " . (($val) ? "true" : "false") . "); \n");
                    } elseif (is_string($val)) {
                        $val = str_replace('"', '\"', $val);
                        if (strlen($val) > 300 || $this->validateDate($val) || $this->validateToken($val)) {
                            print("\$this->assertEquals(!empty(" . $name . $fkey . ") , true); \n");
                        } else {
                            print("\$this->assertEquals(" . $name . $fkey . " , \"" . $val . "\"); \n");
                        }
                    } elseif (is_null($val)) {
                        print("\$this->assertEquals(" . $name . $fkey . " , null); \n");
                    }
                }
            }
        } else {
            if (is_numeric($data)) {
                print("\$this->assertEquals(" . $name . " , " . $data . "); \n");
            } elseif (is_bool($data)) {
                print("\$this->assertEquals(" . $name . " , " . (($data) ? "true" : "false") . "); \n");
            } else {
                print("\$this->assertEquals(" . $name . " , \"" . $data . "\"); \n");
            }
        }
    }

    public function printDocTest($data, $has_data = false)
    {
        return str_replace('"', '', json_encode($this->_printDocTest($data, $has_data), JSON_PRETTY_PRINT));
    }

    public function _printDocTest($data, $has_data = false)
    {
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                if (is_array($val)) {
                    $data[$key] = $this->_printDocTest($val, $has_data);
                } else {
                    $data[$key] = ($has_data === true) ? $val : "<" . gettype($val) . ">";
                }
            }
        }

        return $data;
    }

    public function setIdentity($id, $role = null)
    {
      
        
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $identityMock = $this->getMockBuilder('\Auth\Authentication\Adapter\Model\Identity')
            ->disableOriginalConstructor()
            ->getMock();

        $rbacMock = $this->getMockBuilder('\Rbac\Service\Rbac')
            ->disableOriginalConstructor()
            ->getMock();

        $identityMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($id));
        $session = $serviceManager->get('app_mapper_session')->select(
            $serviceManager->get('app_model_session')->setUid($id)
        )->current();
        $user = ['id' => $id,
                'roles' => [],
                'token' => $session ? str_replace('sess_', '', $session->getToken()) : ($id . '-token'),
                'firstname' => 'toto',
                'avatar' => 'avatar',
                'lastname' => 'tata',
                'organizations' => [['id' => 1],['id' => 3]]];
        if (null !== $role) {
            if (! is_array($role)) {
                $role = [$role];
            }
            foreach ($role as $rr) {
                switch ($rr) {
                case ModelRole::ROLE_ADMIN_ID:
                    $user['roles'][ModelRole::ROLE_ADMIN_ID] = ModelRole::ROLE_ADMIN_STR;
                    break;
                case ModelRole::ROLE_USER_ID:
                    $user['roles'][ModelRole::ROLE_USER_ID] = ModelRole::ROLE_USER_STR;
                    break;
                }
            }
            
        }
        else{
            $user['roles'][ModelRole::ROLE_USER_ID] = ModelRole::ROLE_USER_STR; 
        }
        $userMock = $this->getMockBuilder('\Application\Service\User')
            ->setMethods(['getIdentity'])
            ->getMock();

        $userMock->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($user));

        
   
        
        $serviceManager->get('app_service_user')->getCache()->setItem('identity_'.$id, $user);
        
        $identityMock->expects($this->any())
            ->method('toArray')
            ->will(
                $this->returnValue($user)
            );

        $authMock = $this->getMockBuilder('\Zend\Authentication\AuthenticationService')
            ->getMock();
        
        $storageMock = $this->getMockBuilder('\Auth\Authentication\Storage\CacheBddStorage')
            ->disableOriginalConstructor()
            ->getMock();
        
        $authMock->expects($this->any())
            ->method('getStorage')
            ->will($this->returnValue($storageMock));

        $authMock->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($identityMock));

        $authMock->expects($this->any())
            ->method('hasIdentity')
            ->will($this->returnValue(true));

        $rbacMock->expects($this->any())
            ->method('isGranted')
            ->will($this->returnValue(true));
        
        
       $this->mockMail(true);
        $serviceManager->setService('auth.service', $authMock);
        $serviceManager->setService('rbac.service', $rbacMock);
    }

    public function mockRbac()
    {
        $rbacMock = $this->getMockBuilder('\Rbac\Service\Rbac')
            ->disableOriginalConstructor()
            ->getMock();

        $rbacMock->expects($this->any())
            ->method('isGranted')
            ->will($this->returnValue(true));

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('rbac.service', $rbacMock);
    }
        
    public function mockLinkedin(){
        
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        
        $linkedinMock = $this->getMockBuilder('\LinkedIn\Service')
            ->setMethods(['init','people'])
            ->getMock();
        
        $linkedinMock->expects($this->any())
            ->method('init')
            ->will(
                $this->returnArgument(1)
            );
        
        
        $m_people = new People();
        $m_people->setId('ID')
                ->setFirstname('Paul')
                ->setLastname('BOUSSEKEY')
                ->setPictureUrls(['values' => ['https://avatar.url']]);
        $linkedinMock->expects($this->any())
            ->method('people')
            ->will($this->returnValue($m_people));
        $serviceManager->setService('linkedin.service', $linkedinMock);
    }
    
    public function mockLibrary(){
        
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        
        $libraryMock = $this->getMockBuilder('\Application\Service\Library')
            ->setMethods(['upload'])
            ->getMock();
        
        $libraryMock->expects($this->any())
            ->method('upload')
            ->will(
                $this->returnValue('token')
            );
        
        
      
        $serviceManager->setService('app_service_library', $libraryMock);
    }
    
   
    
    public function mockMail($mockCache = true){
        $mailMock = new \Mail\Service\Mail();
        $m_tplModel = new \Mail\Template\Model\TplModel();
        $m_tplModel->setSubject('subject')->setFrom('from@test.com')->setFromName('fromName');
      
        $cacheMock = $this->getMockBuilder('\Zend\Cache\Storage\StorageInterface')
                ->getMock();
        if($mockCache){
            $cacheMock->expects($this->any())
                ->method('hasItem')
                ->willReturn(true); 
            $cacheMock->expects($this->any())
                ->method('getItem')
                ->willReturn($m_tplModel);
        }
        $s3Mock = $this->getMockBuilder('\Aws\S3\S3Client')
                ->setMethods(["registerStreamWrapper"])
                ->disableOriginalConstructor()
                ->getMock();
        $storageMock = new \Mail\Template\Storage\FsS3Storage();
        $storageMock->setClient($s3Mock);
        $storageMock->setCache($cacheMock);
        $storageMock->init(['bucket' => 'testbucket']);
        $storageMock->setPath("./tmp/");
        $mailMock->setTplStorage($storageMock);
        $mailMock->setOptions(['storage' => [
             'active' => false,
                ],
                'transport' => [
                    'active' => true,
                    'type' => 'sendmail',
                    'options' => [],
                ],
        ]);
        
        $mailMock->init("login", "password");
        
        $transportMock = $this->getMockBuilder('\Zend\Mail\Transport\Smtp')
            ->setMethods(['send'])
            ->getMock();
         $transportMock->expects($this->any())
            ->method('send')
            ->will($this->returnValue(1));  
        $mailMock->setTransport($transportMock);
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('mail.service', $mailMock);
    }
}
