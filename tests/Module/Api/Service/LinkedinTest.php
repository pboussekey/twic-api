<?php
namespace ModuleTest\Api\Service;

use ModuleTest\Api\AbstractService;
use \LinkedIn\Service\Api as LinkedinApi;

class LinkedinTest extends AbstractService
{
    public static $session;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
       
    }

   
    public function testInitService()
    { 
        $mockResponse = $this->getMockBuilder('\Zend\Http\Response')
                ->setMethods(['isSuccess', 'getBody'])->getMock();
        $mockResponse->expects($this->any())
            ->method('isSuccess')
            ->willReturn(true);
        $mockResponse->expects($this->any())
            ->method('getBody')
            ->willReturn(json_encode(
            [
                'id' => 'id',
                'first_name' => 'first_name',
                'last_name' => 'last_name',
                'maiden_name	' => 'maiden_name	',
                'formatted_name' => 'formatted_name',
                'phonetic_first_name' => 'phonetic_first_name',
                'phonetic_last_name' => 'phonetic_last_name',
                'formatted_phonetic_name' => 'formatted_phonetic_name',
                'headline' => 'headline',
                'location' => [ 'name' => 'location_name', 'country' => ['code' => 'FR'] ],
                'industry' => 6,
                'current_share' => 'current_share',
                'num_connections' => 500,
                'num_connections_capped' => 500,
                'summary' => 'summary',
                'specialties' => 'specialties',
                'positions' => [ 
                    'id' => 'id', 
                    'title' => 'title', 
                    'summary' => 'summary', 
                    'start_date' => '01_01_1970', 
                    'end_date' => '01_01_2010',
                    'is_current' => true,
                    'company' => [ 'id' => 'id', 'name' => 'compaty_name', 'type' => 'public', 'industry' => 6, 'ticker' => 'ticker' ]],
                'picture_url' => 'picture_url',
                'picture_urls' => ['values' => ['picture_urls']],
                'site_standard_profile_request' => 'site_standard_profile_request',
                'api_standard_profile_request' => 'api_standard_profile_request',
                'public_profile_url' => 'public_profile_url'

                    ]));
        $mockClient = $this->getMockBuilder('\Zend\Http\Client')
            ->setMethods(['send'])->getMock();
        $mockClient->expects($this->any())
            ->method('send')
            ->willReturn($mockResponse);

        $service = new LinkedinApi($mockClient, 'client_id', 'client_secret', 'api_url', 'redirect_uri');
        
        $data = $service->init('code','token');
        $service->setParams(['param' => 'param']);
        
        $this->assertEquals($data , "token"); 

        return $service;
    }
    

    /**
     * @depends testInitService
     */
       public function testRequest($service)
    { 
        $data = $service->printRequest();
        $this->assertContains('GET api_url HTTP/1.1', $data);
        $this->assertContains('Connection: Keep-Alive', $data); 
        $this->assertContains('uthorization: Bearer token', $data); 
        $this->assertContains('{"param":"param"}', $data); 

    }
    
    /**
     * @depends testInitService
     */
       public function testGetPeople($service)
    { 
        $m_people = $service->people();
        $data = $m_people->toArray();
        $this->assertEquals(count($data) , 22); 
        $this->assertEquals($data['id'] , "id"); 
        $this->assertEquals($data['firstname'] , "first_name"); 
        $this->assertEquals($data['lastname'] , "last_name"); 
        $this->assertEquals($data['maiden_name'] , null); 
        $this->assertEquals($data['formatted_name'] , "formatted_name"); 
        $this->assertEquals($data['phonetic_firstname'] , "phonetic_first_name"); 
        $this->assertEquals($data['phonetic_lastname'] , "phonetic_last_name"); 
        $this->assertEquals($data['formatted_phonetic_name'] , "formatted_phonetic_name"); 
        $this->assertEquals($data['headline'] , "headline"); 
        $this->assertEquals(count($data['location']) , 2); 
        $this->assertEquals($data['location']['name'] , "location_name"); 
        $this->assertEquals(count($data['location']['country']) , 1); 
        $this->assertEquals($data['location']['country']['code'] , "FR"); 
        $this->assertEquals($data['industry'] , 6); 
        $this->assertEquals($data['current_share'] , "current_share"); 
        $this->assertEquals($data['num_connections'] , 500); 
        $this->assertEquals($data['num_connections_capped'] , 500); 
        $this->assertEquals($data['summary'] , "summary"); 
        $this->assertEquals($data['specialties'] , "specialties"); 
        $this->assertEquals(count($data['positions']) , 7); 
        $this->assertEquals($data['positions']['id'] , "id"); 
        $this->assertEquals($data['positions']['title'] , "title"); 
        $this->assertEquals($data['positions']['summary'] , "summary"); 
        $this->assertEquals($data['positions']['start_date'] , "01_01_1970"); 
        $this->assertEquals($data['positions']['end_date'] , "01_01_2010"); 
        $this->assertEquals($data['positions']['is_current'] , true); 
        $this->assertEquals(count($data['positions']['company']) , 5); 
        $this->assertEquals($data['positions']['company']['id'] , "id"); 
        $this->assertEquals($data['positions']['company']['name'] , "compaty_name"); 
        $this->assertEquals($data['positions']['company']['type'] , "public"); 
        $this->assertEquals($data['positions']['company']['industry'] , 6); 
        $this->assertEquals($data['positions']['company']['ticker'] , "ticker"); 
        $this->assertEquals($data['picture_url'] , "picture_url"); 
        $this->assertEquals(count($data['picture_urls']) , 1); 
        $this->assertEquals(count($data['picture_urls']['values']) , 1); 
        $this->assertEquals($data['picture_urls']['values'][0] , "picture_urls"); 
        $this->assertEquals($data['site_standard_profile_request'] , "site_standard_profile_request"); 
        $this->assertEquals($data['api_standard_profile_request'] , "api_standard_profile_request"); 
        $this->assertEquals($data['public_profile_url'] , "public_profile_url"); 

        
        return $m_people;
    }
    
    /**
     * @depends testGetPeople
     */
       public function testPeopleGetters($m_people)
    { 
            $this->assertEquals($m_people->getId() , "id"); 
            $this->assertEquals($m_people->getFirstname() , "first_name"); 
            $this->assertEquals($m_people->getLastname() , "last_name"); 
            $this->assertEquals($m_people->getFormattedName() , "formatted_name"); 
            $this->assertEquals($m_people->getPhoneticFirstname() , "phonetic_first_name"); 
            $this->assertEquals($m_people->getPhoneticLastname() , "phonetic_last_name"); 
            $this->assertEquals($m_people->getFormattedPhoneticName() , "formatted_phonetic_name"); 
            $this->assertEquals($m_people->getHeadline() , "headline"); 
            
            $data = $m_people->getLocation();
            $this->assertEquals(count($data) , 2); 
            $this->assertEquals($data['name'] , "location_name"); 
            $this->assertEquals(count($data['country']) , 1); 
            $this->assertEquals($data['country']['code'] , "FR");  
            $this->assertEquals($m_people->getIndustry() , 6); 
            $this->assertEquals($m_people->getCurrentShare() , "current_share"); 
            $this->assertEquals($m_people->getNumConnections() , 500); 
            $this->assertEquals($m_people->getNumConnectionsCapped() , 500); 
            $this->assertEquals($m_people->getSummary() , "summary"); 
            $this->assertEquals($m_people->getSpecialties() , "specialties"); 
            
            $data = $m_people->getPositions();
            $this->assertEquals(count($data) , 7); 
            $this->assertEquals($data['id'] , "id"); 
            $this->assertEquals($data['title'] , "title"); 
            $this->assertEquals($data['summary'] , "summary"); 
            $this->assertEquals($data['start_date'] , "01_01_1970"); 
            $this->assertEquals($data['end_date'] , "01_01_2010"); 
            $this->assertEquals($data['is_current'] , true); 
            $this->assertEquals(count($data['company']) , 5); 
            $this->assertEquals($data['company']['id'] , "id"); 
            $this->assertEquals($data['company']['name'] , "compaty_name"); 
            $this->assertEquals($data['company']['type'] , "public"); 
            $this->assertEquals($data['company']['industry'] , 6); 
            $this->assertEquals($data['company']['ticker'] , "ticker"); 

            $this->assertEquals($m_people->getPictureUrl() , "picture_url"); 
            $data = $m_people->getPictureUrls();
            $this->assertEquals(count($data) , 1); 
            $this->assertEquals(count($data['values']) , 1); 
            $this->assertEquals($data['values'][0] , "picture_urls"); 
            $this->assertEquals($m_people->getSiteStandardProfileRequest() , "site_standard_profile_request"); 
            $this->assertEquals($m_people->getApiStandardProfileRequest() , "api_standard_profile_request"); 
            $this->assertEquals($m_people->getPublicProfileUrl() , "public_profile_url"); 
           
    }
   
   
}
