<?php
namespace ModuleTest\Api\Service;

use ModuleTest\Api\AbstractService;

class SsoTest extends AbstractService
{
    public static function setUpBeforeClass()
    {
        system('phing -q reset-db deploy-db');
        parent::setUpBeforeClass();
    }
    
    public function testInit()
    {
        $data = $this->jsonRpc('user.login', ['user' => 'crobert@thestudnet.com','password' => 'thestudnet']);
        $this->reset();
        $this->setIdentity(3);
        $data = $this->jsonRpc(
            'user.registerFcm', [
                'uuid' => 3,
                'token' => 'azertyuiop',
                'package' => 'azertyuiop'
            ]
        );
        
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
        
        $this->reset();
        $this->setIdentity(1, 1);
        $data = $this->jsonRpc(
            'page.add', [
            'title' => 'Organization',
            'type'=>'organization',
            'description' => 'description',
            'domaine' => 'yale.twic.io',
            'sso_x509cert' => 'MIIDXTCCAkWgAwIBAgIJALmVVuDWu4NYMA0GCSqGSIb3DQEBCwUAMEUxCzAJBgNVBAYTAkFVMRMwEQYDVQQIDApTb21lLVN0YXRlMSEwHwYDVQQKDBhJbnRlcm5ldCBXaWRnaXRzIFB0eSBMdGQwHhcNMTYxMjMxMTQzNDQ3WhcNNDgwNjI1MTQzNDQ3WjBFMQswCQYDVQQGEwJBVTETMBEGA1UECAwKU29tZS1TdGF0ZTEhMB8GA1UECgwYSW50ZXJuZXQgV2lkZ2l0cyBQdHkgTHRkMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAzUCFozgNb1h1M0jzNRSCjhOBnR+uVbVpaWfXYIR+AhWDdEe5ryY+CgavOg8bfLybyzFdehlYdDRgkedEB/GjG8aJw06l0qF4jDOAw0kEygWCu2mcH7XOxRt+YAH3TVHa/Hu1W3WjzkobqqqLQ8gkKWWM27fOgAZ6GieaJBN6VBSMMcPey3HWLBmc+TYJmv1dbaO2jHhKh8pfKw0W12VM8P1PIO8gv4Phu/uuJYieBWKixBEyy0lHjyixYFCR12xdh4CA47q958ZRGnnDUGFVE1QhgRacJCOZ9bd5t9mr8KLaVBYTCJo5ERE8jymab5dPqe5qKfJsCZiqWglbjUo9twIDAQABo1AwTjAdBgNVHQ4EFgQUxpuwcs/CYQOyui+r1G+3KxBNhxkwHwYDVR0jBBgwFoAUxpuwcs/CYQOyui+r1G+3KxBNhxkwDAYDVR0TBAUwAwEB/zANBgkqhkiG9w0BAQsFAAOCAQEAAiWUKs/2x/viNCKi3Y6blEuCtAGhzOOZ9EjrvJ8+COH3Rag3tVBWrcBZ3/uhhPq5gy9lqw4OkvEws99/5jFsX1FJ6MKBgqfuy7yh5s1YfM0ANHYczMmYpZeAcQf2CGAaVfwTTfSlzNLsF2lW/ly7yapFzlYSJLGoVE+OHEu8g5SlNACUEfkXw+5Eghh+KzlIN7R6Q7r2ixWNFBC/jWf7NKUfJyX8qIG5md1YUeT6GBW9Bm2/1/RiO24JTaYlfLdKK9TYb8sG5B+OLab2DImG99CJ25RkAcSobWNF5zD0O6lgOo3cEdB/ksCq3hmtlC/DlLZ/D8CJ+7VuZnS1rR2naQ==',
            'single_logout_service' => 'http://localhost:8080/simplesaml/saml2/idp/SingleLogoutService.php',
            'single_sign_on_service' => 'http://localhost:8080/simplesaml/saml2/idp/SSOService.php',
            'sso_entity_id' => 'http://localhost:8080/simplesaml/saml2/idp/metadata.php',
            'users' => [
                ['user_id' => 1,'role' => 'admin', 'state' => 'member'],
                ['user_id' => 2,'role' => 'admin', 'state' => 'member'],
                ['user_id' => 3,'role' => 'user', 'state' => 'member'],
                ['user_id' => 4,'role' => 'user', 'state' => 'member'],
                ['user_id' => 5,'role' => 'user', 'state' => 'member'],
            ]
            ]
        );
        
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
        
        return $data['id'];
    }
    
    /**
     * @depends testInit
     */
    public function testLogin($organization_id)
    {
        
        $this->setIdentity(1, 1);
        $data = $this->jsonRpc(
            'saml.login', [
                'organization_id' => $organization_id
            ]
        );

        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals(count($data['result']) , 2);
        $this->assertEquals(!empty($data['result']['url']) , true);
        $this->assertEquals(!empty($data['result']['request_id']) , true);
        $this->assertEquals($data['jsonrpc'] , 2.0);
        
        return $data['result'];
    }
    
    /**
     * @depends testLogin
     */
    public function testValidLogin($login)
    {
        $data = $this->jsonRpc(
            'saml.acs', [
                'request_id' => $login['request_id'],
                'SAMLResponse' => "PHNhbWxwOlJlc3BvbnNlIHhtbG5zOnNhbWxwPSJ1cm46b2FzaXM6bmFtZXM6dGM6U0FNTDoyLjA6cHJvdG9jb2wiIHhtbG5zOnNhbWw9InVybjpvYXNpczpuYW1lczp0YzpTQU1MOjIuMDphc3NlcnRpb24iIElEPSJfNThmMjBmOTQxYTczZWMyMWMxNzgxNDU1ZjllYzEzY2JiNDU2MmFjNGY5IiBWZXJzaW9uPSIyLjAiIElzc3VlSW5zdGFudD0iMjAxOC0xMS0yMFQwNzo1MDozNFoiIERlc3RpbmF0aW9uPSJodHRwOi8vbG9jYWxob3N0OjE0NzIvaW5kZXgucGhwP2FjcyIgSW5SZXNwb25zZVRvPSJPTkVMT0dJTl8wMWMwZDI4OThjY2M1YzZmNThhY2I3NjQxMTJlNzJkNGM2ODMyN2I2Ij48c2FtbDpJc3N1ZXI+aHR0cDovL2xvY2FsaG9zdDo4MDgwL3NpbXBsZXNhbWwvc2FtbDIvaWRwL21ldGFkYXRhLnBocDwvc2FtbDpJc3N1ZXI+PGRzOlNpZ25hdHVyZSB4bWxuczpkcz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC8wOS94bWxkc2lnIyI+CiAgPGRzOlNpZ25lZEluZm8+PGRzOkNhbm9uaWNhbGl6YXRpb25NZXRob2QgQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAxLzEwL3htbC1leGMtYzE0biMiLz4KICAgIDxkczpTaWduYXR1cmVNZXRob2QgQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwLzA5L3htbGRzaWcjcnNhLXNoYTEiLz4KICA8ZHM6UmVmZXJlbmNlIFVSST0iI181OGYyMGY5NDFhNzNlYzIxYzE3ODE0NTVmOWVjMTNjYmI0NTYyYWM0ZjkiPjxkczpUcmFuc2Zvcm1zPjxkczpUcmFuc2Zvcm0gQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwLzA5L3htbGRzaWcjZW52ZWxvcGVkLXNpZ25hdHVyZSIvPjxkczpUcmFuc2Zvcm0gQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAxLzEwL3htbC1leGMtYzE0biMiLz48L2RzOlRyYW5zZm9ybXM+PGRzOkRpZ2VzdE1ldGhvZCBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvMDkveG1sZHNpZyNzaGExIi8+PGRzOkRpZ2VzdFZhbHVlPmdlWVNCMzh2blVXUzNBMTZCclUwYUVNUkxmRT08L2RzOkRpZ2VzdFZhbHVlPjwvZHM6UmVmZXJlbmNlPjwvZHM6U2lnbmVkSW5mbz48ZHM6U2lnbmF0dXJlVmFsdWU+U2pRbTFXbUJxN1Z0elRzVGVJeWVKVVZRNEhWdXEwSjcwTnlUZ2MrTlovU3ZBT2MrN0xXaHlDQjg5VHlLYkkyVWVpcXlGRTNmeUhxUXFsQmpxbUp5SElQM2w2R1V1elV0YzBDZGszclFxd2c3ZGw2d2QvNWtBU1d5cy9jYTNJYUR1SUd0N1NuUnFyazRRdlVtRUJkSHR3WWkwQ0g4UDl6eFl0VnhFeHQyaTZ3bWJoV0JRZEsrOXhJOHFLOStlUFZFRUFyR2FOdGNhUGgxYkNWREhKSkhaQXkyRTVYRGZNQVZyU1FHZEJVUFF1ME5xeVd5YUg4eitrSVBIT2NpNXZWcXdlcXhBT2xYR0pnL2g4TFkzcjI2SExDbmV0SlhFam9XUmpNVU9aS3VJSTBqb1RLSERpdzZkOGJhS2pYSmNjbnFieXlFUlZERVBYLzRlOXBaQXpGaXp3PT08L2RzOlNpZ25hdHVyZVZhbHVlPgo8ZHM6S2V5SW5mbz48ZHM6WDUwOURhdGE+PGRzOlg1MDlDZXJ0aWZpY2F0ZT5NSUlEWFRDQ0FrV2dBd0lCQWdJSkFMbVZWdURXdTROWU1BMEdDU3FHU0liM0RRRUJDd1VBTUVVeEN6QUpCZ05WQkFZVEFrRlZNUk13RVFZRFZRUUlEQXBUYjIxbExWTjBZWFJsTVNFd0h3WURWUVFLREJoSmJuUmxjbTVsZENCWGFXUm5hWFJ6SUZCMGVTQk1kR1F3SGhjTk1UWXhNak14TVRRek5EUTNXaGNOTkRnd05qSTFNVFF6TkRRM1dqQkZNUXN3Q1FZRFZRUUdFd0pCVlRFVE1CRUdBMVVFQ0F3S1UyOXRaUzFUZEdGMFpURWhNQjhHQTFVRUNnd1lTVzUwWlhKdVpYUWdWMmxrWjJsMGN5QlFkSGtnVEhSa01JSUJJakFOQmdrcWhraUc5dzBCQVFFRkFBT0NBUThBTUlJQkNnS0NBUUVBelVDRm96Z05iMWgxTTBqek5SU0NqaE9CblIrdVZiVnBhV2ZYWUlSK0FoV0RkRWU1cnlZK0NnYXZPZzhiZkx5Ynl6RmRlaGxZZERSZ2tlZEVCL0dqRzhhSncwNmwwcUY0akRPQXcwa0V5Z1dDdTJtY0g3WE94UnQrWUFIM1RWSGEvSHUxVzNXanprb2JxcXFMUThna0tXV00yN2ZPZ0FaNkdpZWFKQk42VkJTTU1jUGV5M0hXTEJtYytUWUptdjFkYmFPMmpIaEtoOHBmS3cwVzEyVk04UDFQSU84Z3Y0UGh1L3V1SllpZUJXS2l4QkV5eTBsSGp5aXhZRkNSMTJ4ZGg0Q0E0N3E5NThaUkdubkRVR0ZWRTFRaGdSYWNKQ09aOWJkNXQ5bXI4S0xhVkJZVENKbzVFUkU4anltYWI1ZFBxZTVxS2ZKc0NaaXFXZ2xialVvOXR3SURBUUFCbzFBd1RqQWRCZ05WSFE0RUZnUVV4cHV3Y3MvQ1lRT3l1aStyMUcrM0t4Qk5oeGt3SHdZRFZSMGpCQmd3Rm9BVXhwdXdjcy9DWVFPeXVpK3IxRyszS3hCTmh4a3dEQVlEVlIwVEJBVXdBd0VCL3pBTkJna3Foa2lHOXcwQkFRc0ZBQU9DQVFFQUFpV1VLcy8yeC92aU5DS2kzWTZibEV1Q3RBR2h6T09aOUVqcnZKOCtDT0gzUmFnM3RWQldyY0JaMy91aGhQcTVneTlscXc0T2t2RXdzOTkvNWpGc1gxRko2TUtCZ3FmdXk3eWg1czFZZk0wQU5IWWN6TW1ZcFplQWNRZjJDR0FhVmZ3VFRmU2x6TkxzRjJsVy9seTd5YXBGemxZU0pMR29WRStPSEV1OGc1U2xOQUNVRWZrWHcrNUVnaGgrS3psSU43UjZRN3IyaXhXTkZCQy9qV2Y3TktVZkp5WDhxSUc1bWQxWVVlVDZHQlc5Qm0yLzEvUmlPMjRKVGFZbGZMZEtLOVRZYjhzRzVCK09MYWIyREltRzk5Q0oyNVJrQWNTb2JXTkY1ekQwTzZsZ09vM2NFZEIva3NDcTNobXRsQy9EbExaL0Q4Q0orN1Z1Wm5TMXJSMm5hUT09PC9kczpYNTA5Q2VydGlmaWNhdGU+PC9kczpYNTA5RGF0YT48L2RzOktleUluZm8+PC9kczpTaWduYXR1cmU+PHNhbWxwOlN0YXR1cz48c2FtbHA6U3RhdHVzQ29kZSBWYWx1ZT0idXJuOm9hc2lzOm5hbWVzOnRjOlNBTUw6Mi4wOnN0YXR1czpTdWNjZXNzIi8+PC9zYW1scDpTdGF0dXM+PHNhbWw6QXNzZXJ0aW9uIHhtbG5zOnhzaT0iaHR0cDovL3d3dy53My5vcmcvMjAwMS9YTUxTY2hlbWEtaW5zdGFuY2UiIHhtbG5zOnhzPSJodHRwOi8vd3d3LnczLm9yZy8yMDAxL1hNTFNjaGVtYSIgSUQ9Il9mZjU3ZDUwZTQzMmJiMTAzZDEyYjZmOGMxMzdkMDAzNjNkNjM3NTMzY2QiIFZlcnNpb249IjIuMCIgSXNzdWVJbnN0YW50PSIyMDE4LTExLTIwVDA3OjUwOjM0WiI+PHNhbWw6SXNzdWVyPmh0dHA6Ly9sb2NhbGhvc3Q6ODA4MC9zaW1wbGVzYW1sL3NhbWwyL2lkcC9tZXRhZGF0YS5waHA8L3NhbWw6SXNzdWVyPjxkczpTaWduYXR1cmUgeG1sbnM6ZHM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvMDkveG1sZHNpZyMiPgogIDxkczpTaWduZWRJbmZvPjxkczpDYW5vbmljYWxpemF0aW9uTWV0aG9kIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMS8xMC94bWwtZXhjLWMxNG4jIi8+CiAgICA8ZHM6U2lnbmF0dXJlTWV0aG9kIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMC8wOS94bWxkc2lnI3JzYS1zaGExIi8+CiAgPGRzOlJlZmVyZW5jZSBVUkk9IiNfZmY1N2Q1MGU0MzJiYjEwM2QxMmI2ZjhjMTM3ZDAwMzYzZDYzNzUzM2NkIj48ZHM6VHJhbnNmb3Jtcz48ZHM6VHJhbnNmb3JtIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMC8wOS94bWxkc2lnI2VudmVsb3BlZC1zaWduYXR1cmUiLz48ZHM6VHJhbnNmb3JtIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMS8xMC94bWwtZXhjLWMxNG4jIi8+PC9kczpUcmFuc2Zvcm1zPjxkczpEaWdlc3RNZXRob2QgQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwLzA5L3htbGRzaWcjc2hhMSIvPjxkczpEaWdlc3RWYWx1ZT5zR1E4allucFZqN20wNG1WMEhyV3grY2s5U2s9PC9kczpEaWdlc3RWYWx1ZT48L2RzOlJlZmVyZW5jZT48L2RzOlNpZ25lZEluZm8+PGRzOlNpZ25hdHVyZVZhbHVlPk85eUlKa1M0S1A1RmVDclVCYzY1c29zMmJYSmVLTCtCZE9rcS9iWk8zQmZvYWtXVXczYUcyOSsxQzM1bFRtRVM3QTI0d2phT2Rta0duVXd4aUdpUlF0cWFMNmtTR3VnWWFOcGNwOGNEOEloMGpaVHBvc2tUVlR1WUNpWkFuTkRMdXgwcllFcXlWSWFSUmZWYjQ3YUNPbTNiZ1hjdW9peWZMUEVVNU90cW9LRW9YdWltdDdzOU0wMnJPc1BiYk1wVWxwU2hpUC81SWJpQWVxMGlaMUZNNVZ6T0JtRHlGQ3c4UjlDUVBHL1F2VjBWYjN3bVJQNE9CdGVkamYvZ2ZST3k2dWFTVk04ZVlTZ21oenBBbE0rM2E1S2hTTmlWcitNNHgxY3VGcHlQUnptOEh5b3JMWXBQRTBFanlpLzFJYktKczdsd253KzZhU0o2YlVVS1NKaWxBQT09PC9kczpTaWduYXR1cmVWYWx1ZT4KPGRzOktleUluZm8+PGRzOlg1MDlEYXRhPjxkczpYNTA5Q2VydGlmaWNhdGU+TUlJRFhUQ0NBa1dnQXdJQkFnSUpBTG1WVnVEV3U0TllNQTBHQ1NxR1NJYjNEUUVCQ3dVQU1FVXhDekFKQmdOVkJBWVRBa0ZWTVJNd0VRWURWUVFJREFwVGIyMWxMVk4wWVhSbE1TRXdId1lEVlFRS0RCaEpiblJsY201bGRDQlhhV1JuYVhSeklGQjBlU0JNZEdRd0hoY05NVFl4TWpNeE1UUXpORFEzV2hjTk5EZ3dOakkxTVRRek5EUTNXakJGTVFzd0NRWURWUVFHRXdKQlZURVRNQkVHQTFVRUNBd0tVMjl0WlMxVGRHRjBaVEVoTUI4R0ExVUVDZ3dZU1c1MFpYSnVaWFFnVjJsa1oybDBjeUJRZEhrZ1RIUmtNSUlCSWpBTkJna3Foa2lHOXcwQkFRRUZBQU9DQVE4QU1JSUJDZ0tDQVFFQXpVQ0ZvemdOYjFoMU0wanpOUlNDamhPQm5SK3VWYlZwYVdmWFlJUitBaFdEZEVlNXJ5WStDZ2F2T2c4YmZMeWJ5ekZkZWhsWWREUmdrZWRFQi9Hakc4YUp3MDZsMHFGNGpET0F3MGtFeWdXQ3UybWNIN1hPeFJ0K1lBSDNUVkhhL0h1MVczV2p6a29icXFxTFE4Z2tLV1dNMjdmT2dBWjZHaWVhSkJONlZCU01NY1BleTNIV0xCbWMrVFlKbXYxZGJhTzJqSGhLaDhwZkt3MFcxMlZNOFAxUElPOGd2NFBodS91dUpZaWVCV0tpeEJFeXkwbEhqeWl4WUZDUjEyeGRoNENBNDdxOTU4WlJHbm5EVUdGVkUxUWhnUmFjSkNPWjliZDV0OW1yOEtMYVZCWVRDSm81RVJFOGp5bWFiNWRQcWU1cUtmSnNDWmlxV2dsYmpVbzl0d0lEQVFBQm8xQXdUakFkQmdOVkhRNEVGZ1FVeHB1d2NzL0NZUU95dWkrcjFHKzNLeEJOaHhrd0h3WURWUjBqQkJnd0ZvQVV4cHV3Y3MvQ1lRT3l1aStyMUcrM0t4Qk5oeGt3REFZRFZSMFRCQVV3QXdFQi96QU5CZ2txaGtpRzl3MEJBUXNGQUFPQ0FRRUFBaVdVS3MvMngvdmlOQ0tpM1k2YmxFdUN0QUdoek9PWjlFanJ2SjgrQ09IM1JhZzN0VkJXcmNCWjMvdWhoUHE1Z3k5bHF3NE9rdkV3czk5LzVqRnNYMUZKNk1LQmdxZnV5N3loNXMxWWZNMEFOSFljek1tWXBaZUFjUWYyQ0dBYVZmd1RUZlNsek5Mc0YybFcvbHk3eWFwRnpsWVNKTEdvVkUrT0hFdThnNVNsTkFDVUVma1h3KzVFZ2hoK0t6bElON1I2UTdyMml4V05GQkMvaldmN05LVWZKeVg4cUlHNW1kMVlVZVQ2R0JXOUJtMi8xL1JpTzI0SlRhWWxmTGRLSzlUWWI4c0c1QitPTGFiMkRJbUc5OUNKMjVSa0FjU29iV05GNXpEME82bGdPbzNjRWRCL2tzQ3EzaG10bEMvRGxMWi9EOENKKzdWdVpuUzFyUjJuYVE9PTwvZHM6WDUwOUNlcnRpZmljYXRlPjwvZHM6WDUwOURhdGE+PC9kczpLZXlJbmZvPjwvZHM6U2lnbmF0dXJlPjxzYW1sOlN1YmplY3Q+PHNhbWw6TmFtZUlEIFNQTmFtZVF1YWxpZmllcj0iaHR0cDovL2FwcC5leGFtcGxlLmNvbSIgRm9ybWF0PSJ1cm46b2FzaXM6bmFtZXM6dGM6U0FNTDoyLjA6bmFtZWlkLWZvcm1hdDp0cmFuc2llbnQiPl80NzI5ZWMwYmNlZDYyOGU5MjdkZWU5ZDJiMjZlMTk4NDYxMGY3NTQ0Zjg8L3NhbWw6TmFtZUlEPjxzYW1sOlN1YmplY3RDb25maXJtYXRpb24gTWV0aG9kPSJ1cm46b2FzaXM6bmFtZXM6dGM6U0FNTDoyLjA6Y206YmVhcmVyIj48c2FtbDpTdWJqZWN0Q29uZmlybWF0aW9uRGF0YSBOb3RPbk9yQWZ0ZXI9IjIwMTgtMTEtMjBUMDc6NTU6MzRaIiBSZWNpcGllbnQ9Imh0dHA6Ly9sb2NhbGhvc3Q6MTQ3Mi9pbmRleC5waHA/YWNzIiBJblJlc3BvbnNlVG89Ik9ORUxPR0lOXzAxYzBkMjg5OGNjYzVjNmY1OGFjYjc2NDExMmU3MmQ0YzY4MzI3YjYiLz48L3NhbWw6U3ViamVjdENvbmZpcm1hdGlvbj48L3NhbWw6U3ViamVjdD48c2FtbDpDb25kaXRpb25zIE5vdEJlZm9yZT0iMjAxOC0xMS0yMFQwNzo1MDowNFoiIE5vdE9uT3JBZnRlcj0iMjAxOC0xMS0yMFQwNzo1NTozNFoiPjxzYW1sOkF1ZGllbmNlUmVzdHJpY3Rpb24+PHNhbWw6QXVkaWVuY2U+aHR0cDovL2FwcC5leGFtcGxlLmNvbTwvc2FtbDpBdWRpZW5jZT48L3NhbWw6QXVkaWVuY2VSZXN0cmljdGlvbj48L3NhbWw6Q29uZGl0aW9ucz48c2FtbDpBdXRoblN0YXRlbWVudCBBdXRobkluc3RhbnQ9IjIwMTgtMTEtMjBUMDc6NTA6MzRaIiBTZXNzaW9uTm90T25PckFmdGVyPSIyMDE4LTExLTIwVDE1OjUwOjM0WiIgU2Vzc2lvbkluZGV4PSJfMGQ0Yjg2OTQzYTNiMTU4MWE4NTZhYWZlNDAxYzZjNGYwNGZhNjgwN2E0Ij48c2FtbDpBdXRobkNvbnRleHQ+PHNhbWw6QXV0aG5Db250ZXh0Q2xhc3NSZWY+dXJuOm9hc2lzOm5hbWVzOnRjOlNBTUw6Mi4wOmFjOmNsYXNzZXM6UGFzc3dvcmQ8L3NhbWw6QXV0aG5Db250ZXh0Q2xhc3NSZWY+PC9zYW1sOkF1dGhuQ29udGV4dD48L3NhbWw6QXV0aG5TdGF0ZW1lbnQ+PHNhbWw6QXR0cmlidXRlU3RhdGVtZW50PjxzYW1sOkF0dHJpYnV0ZSBOYW1lPSJ1aWQiIE5hbWVGb3JtYXQ9InVybjpvYXNpczpuYW1lczp0YzpTQU1MOjIuMDphdHRybmFtZS1mb3JtYXQ6YmFzaWMiPjxzYW1sOkF0dHJpYnV0ZVZhbHVlIHhzaTp0eXBlPSJ4czpzdHJpbmciPjE8L3NhbWw6QXR0cmlidXRlVmFsdWU+PC9zYW1sOkF0dHJpYnV0ZT48c2FtbDpBdHRyaWJ1dGUgTmFtZT0iZWR1UGVyc29uQWZmaWxpYXRpb24iIE5hbWVGb3JtYXQ9InVybjpvYXNpczpuYW1lczp0YzpTQU1MOjIuMDphdHRybmFtZS1mb3JtYXQ6YmFzaWMiPjxzYW1sOkF0dHJpYnV0ZVZhbHVlIHhzaTp0eXBlPSJ4czpzdHJpbmciPmdyb3VwMTwvc2FtbDpBdHRyaWJ1dGVWYWx1ZT48L3NhbWw6QXR0cmlidXRlPjxzYW1sOkF0dHJpYnV0ZSBOYW1lPSJlbWFpbCIgTmFtZUZvcm1hdD0idXJuOm9hc2lzOm5hbWVzOnRjOlNBTUw6Mi4wOmF0dHJuYW1lLWZvcm1hdDpiYXNpYyI+PHNhbWw6QXR0cmlidXRlVmFsdWUgeHNpOnR5cGU9InhzOnN0cmluZyI+dXNlcjFAZXhhbXBsZS5jb208L3NhbWw6QXR0cmlidXRlVmFsdWU+PC9zYW1sOkF0dHJpYnV0ZT48c2FtbDpBdHRyaWJ1dGUgTmFtZT0icHJlZmVycmVkTGFuZ3VhZ2UiIE5hbWVGb3JtYXQ9InVybjpvYXNpczpuYW1lczp0YzpTQU1MOjIuMDphdHRybmFtZS1mb3JtYXQ6YmFzaWMiPjxzYW1sOkF0dHJpYnV0ZVZhbHVlIHhzaTp0eXBlPSJ4czpzdHJpbmciPmVuPC9zYW1sOkF0dHJpYnV0ZVZhbHVlPjwvc2FtbDpBdHRyaWJ1dGU+PC9zYW1sOkF0dHJpYnV0ZVN0YXRlbWVudD48L3NhbWw6QXNzZXJ0aW9uPjwvc2FtbHA6UmVzcG9uc2U+\n",
            ]);
        
        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals(count($data['result']) , 20);
        $this->assertEquals($data['result']['id'] , 8);
        $this->assertEquals(!empty($data['result']['token']) , true);
        $this->assertEquals(!empty($data['result']['created_date']) , true);
        $this->assertEquals($data['result']['firstname'] , null);
        $this->assertEquals($data['result']['lastname'] , null);
        $this->assertEquals($data['result']['nickname'] , null);
        $this->assertEquals($data['result']['suspension_date'] , null);
        $this->assertEquals($data['result']['suspension_reason'] , null);
        $this->assertEquals($data['result']['organization_id'] , 1);
        $this->assertEquals($data['result']['email'] , null);
        $this->assertEquals($data['result']['avatar'] , null);
        $this->assertEquals($data['result']['expiration_date'] , null);
        $this->assertEquals($data['result']['has_linkedin'] , false);
        $this->assertEquals($data['result']['has_sso'] , true);
        $this->assertEquals($data['result']['cgu_accepted'] , 0);
        $this->assertEquals($data['result']['swap_email'] , null);
        $this->assertEquals($data['result']['cache'] , null);
        $this->assertEquals(count($data['result']['roles']) , 1);
        $this->assertEquals($data['result']['roles'][2] , "user");
        $this->assertEquals(!empty($data['result']['wstoken']) , true);
        $this->assertEquals(!empty($data['result']['fbtoken']) , true);
        $this->assertEquals($data['jsonrpc'] , 2.0);
        
        return $data['result']['id'];
    }

    /**
     * @depends testValidLogin
     */
    public function testLogout($user_id)
    {
        $this->setIdentity($user_id);
        $data = $this->jsonRpc('saml.logout', []);
        
        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals(count($data['result']) , 2);
        $this->assertEquals(!empty($data['result']['url']) , true);
        $this->assertEquals(!empty($data['result']['logout_request_id']) , true);
        $this->assertEquals($data['jsonrpc'] , 2.0);
    }
    
    /**
     * @depends testValidLogin
     */
    public function testVerifieLogout($user_id)
    {
        $this->setIdentity($user_id);
        $data = $this->jsonRpc(
        'saml.sls', [
            'request_id' => 'request_id',
            'SAMLResponse' => "fZLdagIxEIVfZcm9Jtm/rEEtpZYiWIUqXvRGxmSsC7vJspMFH7+7llJLizeBzMx35nCSKUFdNXrlP3wX3pAa7wijS1050tfWjHWt0x6oJO2gRtLB6O3j60rHY6Gb1gdvfMVukPsEEGEbSu9YtFzM2EEpk8hUJio/2UQmxyTGVCWQxgVkouirAgBP0mY5i/bYUk/OWC/U40QdLh0FcKEvCVmMpBzFYiellrkW6TuLFkihdBCu1DmERnNeeQPV2VPQMlUxL53Fy7g5Nw9UUa/qvlPY+RnbrJ9Xm5fl+oDS2jxBdTQTJSamMBBn5qiKTKoMxNFKkMUEM8Xm0yEDfTXXzv+sLEQhOJV1U+Ewx4ej92AbXmMACwEGK1N+KzL9eqNtgNDR79uTtxjtoerwfup0ndbbzhgkYnz+teFHlP/3D+af\n",
        ]);
        
        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals($data['result'] , true);
        $this->assertEquals($data['jsonrpc'] , 2.0);
    }
    
    /**
     * @depends testValidLogin
     */
    public function testLogoutRequest($user_id)
    {
        $this->setIdentity($user_id);
        $data = $this->jsonRpc(
            'saml.slsr', [
                'SAMLRequest' => "fZLdagIxEIVfZcm9Jtm/rEEtpZYiWIUqXvRGxmSsC7vJspMFH7+7llJLizeBzMx35nCSKUFdNXrlP3wX3pAa7wijS1050tfWjHWt0x6oJO2gRtLB6O3j60rHY6Gb1gdvfMVukPsEEGEbSu9YtFzM2EEpk8hUJio/2UQmxyTGVCWQxgVkouirAgBP0mY5i/bYUk/OWC/U40QdLh0FcKEvCVmMpBzFYiellrkW6TuLFkihdBCu1DmERnNeeQPV2VPQMlUxL53Fy7g5Nw9UUa/qvlPY+RnbrJ9Xm5fl+oDS2jxBdTQTJSamMBBn5qiKTKoMxNFKkMUEM8Xm0yEDfTXXzv+sLEQhOJV1U+Ewx4ej92AbXmMACwEGK1N+KzL9eqNtgNDR79uTtxjtoerwfup0ndbbzhgkYnz+teFHlP/3D+af\n",
            ]);
        
        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals(!empty($data['result']) , true);
        $this->assertEquals($data['jsonrpc'] , 2.0);
    }
    
}
