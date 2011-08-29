<?php
/**
 * @package cms
 * @subpackage tests
 */
class ContentControllerPermissionTest extends FunctionalTest {
	
	protected $usesDatabase = true;
	
	protected $autoFollowRedirection = false;
	
	public function testCanViewStage() {
		$page = new Page();
		$page->URLSegment = 'testpage';
		$page->write();
		$page->publish('Stage', 'Live');
		
		$response = $this->get('/testpage');
		$this->assertEquals($response->getStatusCode(), 200, 'Doesnt require login for implicit live stage');
		
		$response = $this->get('/testpage/?stage=Live');
		$this->assertEquals($response->getStatusCode(), 200, 'Doesnt require login for explicit live stage');
		
		$response = $this->get('/testpage/?stage=Stage');
		// should redirect to login
		$this->assertEquals($response->getStatusCode(), 302, 'Redirects to login page when not logged in for draft stage');
		$this->assertContains('Security/login', $response->getHeader('Location'));
		
		$this->logInWithPermission('CMS_ACCESS_CMSMain');
		
		$response = $this->get('/testpage/?stage=Stage');
		$this->assertEquals($response->getStatusCode(), 302, 'Redirects to page view in CMS when logged in for draft stage');
		$this->assertNotContains('Security/login', $response->getHeader('Location'));
		$this->assertContains('admin/page/edit/show/' . $page->ID, $response->getHeader('Location'));
	}
	
	
}