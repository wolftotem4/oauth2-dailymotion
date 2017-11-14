<?php

namespace WTotem4\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class DailymotionUser implements ResourceOwnerInterface
{
    /**
     * @var array
     */
    protected $data;

    /**
     * DailymotionUser constructor.
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->data = $response;
    }

    /**
     * @return string|null
     */
    public function getId()
    {
        return $this->getField('id');
    }

    /**
     * @return string|null
     */
    public function getUsername()
    {
        return $this->getField('username');
    }

    /**
     * @return string|null
     */
    public function getScreenname()
    {
        return $this->getField('screenname');
    }

    /**
     * @return string|null
     */
    public function getAvatar()
    {
        return $this->getField('avatar_120_url');
    }

    /**
     * @return string|null
     */
    public function getProfileUrl()
    {
        return $this->getField('url');
    }

    /**
     * Get user data as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    private function getField($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }
}
