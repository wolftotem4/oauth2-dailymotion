<?php

namespace Tests\Unit\Provider;

use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;
use WTotem4\OAuth2\Client\Provider\Dailymotion;

class FooDailymotionProvider extends Dailymotion
{
    protected function fetchResourceOwnerDetails(AccessToken $token)
    {
        return [
            'avatar_120_url'    => 'http://avatar_url',
            'email'             => 'mock_email',
            'id'                => 'mock_id',
            'screenname'        => 'mock_screenname',
            'url'               => 'http://url',
            'username'          => 'mock_username'
        ];
    }

    public function callCheckResponse(...$args)
    {
        $this->checkResponse(...$args);
    }
}

class DailymotionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FooDailymotionProvider
     */
    protected $provider;

    /**
     * @var \Mockery\MockInterface|\League\OAuth2\Client\Token\AccessToken
     */
    protected $token;

    /**
     * @var \Mockery\MockInterface|\Psr\Http\Message\ResponseInterface
     */
    protected $responseMock;

    protected function setUp()
    {
        parent::setUp();

        $this->provider = new FooDailymotionProvider([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none',
        ]);

        $this->token = \Mockery::mock(AccessToken::class);

        $this->responseMock = \Mockery::mock(ResponseInterface::class);
    }

    protected function tearDown()
    {
        \Mockery::close();

        parent::tearDown();
    }

    public function testGetAndSetUserFields()
    {
        $faker = \Faker\Factory::create();

        $userFields = $faker->words(3, false);

        $this->provider->setUserFields($userFields);

        $returnValue = $this->provider->getUserFields();

        $this->assertSame($userFields, $returnValue);
    }

    public function testGetResourceOwnerDetailsUrl()
    {
        $userFields = ['id', 'username', 'screenname'];

        $this->provider->setUserFields($userFields);

        $returnValue = $this->provider->getResourceOwnerDetailsUrl($this->token);

        parse_str(parse_url($returnValue, PHP_URL_QUERY), $query);

        $returnFields = explode(',', $query['fields']);

        $this->assertSame($userFields, $returnFields);
    }

    public function testUserData()
    {
        $userData = $this->provider->getResourceOwner($this->token);

        $this->assertEquals('mock_id', $userData->getId());
        $this->assertEquals('mock_username', $userData->getUsername());
        $this->assertEquals('mock_screenname', $userData->getScreenname());
        $this->assertEquals('http://avatar_url', $userData->getAvatar());
        $this->assertEquals('http://url', $userData->getProfileUrl());
        $this->assertEquals('mock_email', $userData->getEmail());
    }

    /**
     * @expectedException        \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     * @expectedExceptionCode    400
     * @expectedExceptionMessage An OAuth Error
     */
    public function testProperlyHandlesErrorResponses()
    {
        $code       = 400;
        $message    = 'An OAuth Error';
        $response   = ['error' => ['more_info' => 'http://mock_url', 'code' => $code, 'message' => $message]];

        $this->provider->callCheckResponse($this->responseMock, $response);
    }

    /**
     * @expectedException        \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     * @expectedExceptionMessage An Error Description
     */
    public function testErrorDescriptionResponses()
    {
        $error              = 'mock_error';
        $error_description  = 'An Error Description';
        $response           = compact('error', 'error_description');

        $this->provider->callCheckResponse($this->responseMock, $response);
    }
}
