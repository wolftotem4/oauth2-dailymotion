<?php

namespace WTotem4\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class Dailymotion extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * @var array
     */
    protected $userFields = [
        'id', 'username', 'screenname',
        'description', 'url', 'avatar_120_url'
    ];

    /**
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return 'https://www.dailymotion.com/oauth/authorize';
    }

    /**
     * @param  array  $params
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return 'https://api.dailymotion.com/oauth/token';
    }

    /**
     * @param  \League\OAuth2\Client\Token\AccessToken  $token
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return 'https://api.dailymotion.com/user/me?fields=' . urlencode(implode(',', $this->userFields));
    }

    /**
     * @return array
     */
    public function getUserFields()
    {
        return $this->userFields;
    }

    /**
     * @param  array  $userFields
     * @return $this
     */
    public function setUserFields(array $userFields)
    {
        $this->userFields = $userFields;

        return $this;
    }

    /**
     * @return array
     */
    protected function getDefaultScopes()
    {
        return [];
    }

    /**
     * @param  \Psr\Http\Message\ResponseInterface  $response
     * @param  array  $data
     *
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (! empty($data['error'])) {

            if (is_array($error = $data['error'])) {
                $code   = $error['code'];
                $error  = $error['message'];
            } else {
                $code   = 0;
                $error  = isset($data['error_description']) ? $data['error_description'] : $data['error'];
            }

            throw new IdentityProviderException($error, $code, $data);
        }
    }

    /**
     * @param  array  $response
     * @param  \League\OAuth2\Client\Token\AccessToken  $token
     * @return \WTotem4\OAuth2\Client\Provider\DailymotionUser
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new DailymotionUser($response);
    }
}
