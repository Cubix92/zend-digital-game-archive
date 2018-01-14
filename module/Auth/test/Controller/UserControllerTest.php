<?php

namespace UserTest\Controller;

use Auth\Controller\UserController;
use Auth\Form\UserForm;
use Auth\Model\User;
use Auth\Model\UserTable;
use Prophecy\Argument;
use Zend\Authentication\AuthenticationService;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Form\FormElementManager;
use Zend\Stdlib\ArrayUtils;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class UserControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;

    protected $userTable;

    public function setUp()
    {
        $configOverrides = [];

        $this->setApplicationConfig(ArrayUtils::merge(
            include __DIR__ . '/../../../../config/application.config.php',
            $configOverrides
        ));

        parent::setUp();

        $this->getApplicationServiceLocator()->setAllowOverride(true);

        $config = $this->getApplicationServiceLocator()->get('config');
        $config['db'] = [];

        $this->getApplicationServiceLocator()->setService('config', $config);
        $this->getApplicationServiceLocator()->setService(AuthenticationService::class, $this->mockAuthenticationService()->reveal());
        $this->getApplicationServiceLocator()->setService(UserTable::class, $this->mockUserTable()->reveal());

        $this->getApplicationServiceLocator()->setAllowOverride(false);
    }

    protected function mockAuthenticationService()
    {
        $authenticationService = $this->prophesize(AuthenticationService::class);
        $authenticationService->hasIdentity()->willReturn(true);
        $authenticationService->getIdentity()->willReturn((object)['email' => 'example@example.com']);

        return $authenticationService;
    }

    protected function mockUserTable()
    {
        $this->userTable = $this->prophesize(UserTable::class);
        return $this->userTable;
    }

    public function testIndexActionCanBeAccessed()
    {
        $resultSet = $this->prophesize(ResultSetInterface::class)->reveal();
        $this->userTable->fetchAll()->willReturn($resultSet);

        $this->dispatch('/user', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('auth');
        $this->assertControllerName(UserController::class);
        $this->assertControllerClass('UserController');
        $this->assertMatchedRouteName('user');
    }

    public function testAddActionWithoutPost()
    {
        $this->dispatch('/user/add');

        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('user');
    }

    public function testAddActionRedirectsAfterValidPost()
    {
        $this->userTable
            ->save(Argument::type(User::class))
            ->shouldBeCalled();

        $userForm = $this->getApplicationServiceLocator()
            ->get(FormElementManager::class)->get(UserForm::class);

        $postData = [
            'email'  => 'example@example.com',
            'role' => 'admin',
            'password' => 'test1234',
            'repeat_password' => 'test1234',
            'csrf' => $userForm->get('csrf')->getValue()
        ];

        $this->dispatch('/user/add', 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/user');
    }

    public function testEditActionWithoutPost()
    {
        $user = (new User())->setEmail('example@example.com');
        $this->userTable->getUser(Argument::type('integer'))->willReturn($user);

        $this->userTable
            ->getUser(Argument::type('integer'))
            ->shouldBeCalled();

        $this->dispatch('/user/edit/1');

        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('user');
    }

    public function testEditActionRedirectsAfterValidPost()
    {
        $user = (new User())->setEmail('example@example.com');
        $this->userTable->getUser(Argument::type('integer'))->willReturn($user);

        $this->userTable
            ->save(Argument::type(User::class))
            ->shouldBeCalled();

        $userForm = $this->getApplicationServiceLocator()
            ->get(FormElementManager::class)->get(UserForm::class);

        $postData = [
            'id' => 1,
            'email'  => 'example@example.com',
            'role' => 'admin',
            'csrf' => $userForm->get('csrf')->getValue()
        ];

        $this->dispatch('/user/edit/1', 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/user');
    }

    public function testDeleteActionRedirectsAfterValidPost()
    {
        $user = (new User())->setId(1);

        $this->userTable->getUser(Argument::type('integer'))->willReturn($user);

        $this->userTable
            ->delete(Argument::type('integer'))
            ->shouldBeCalled();

        $this->dispatch('/user/delete/1');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/user');
    }
}
