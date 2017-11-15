<?php

namespace Tests\Unit\Provider;

use WTotem4\OAuth2\Client\Provider\DailymotionUser;

class DailymotionUserTest extends \PHPUnit_Framework_TestCase
{
    public function testUnitData()
    {
        $faker = \Faker\Factory::create();

        $id             = $faker->word;
        $username       = $faker->userName;
        $screenname     = $faker->name;
        $email          = $faker->email;
        $url            = $faker->url;
        $avatar_120_url = $faker->imageUrl;

        $data = compact('id', 'username', 'email', 'screenname', 'avatar_120_url', 'url');
        $user = new DailymotionUser($data);

        $this->assertEquals($id, $user->getId());
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($screenname, $user->getScreenname());
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($avatar_120_url, $user->getAvatar());
        $this->assertEquals($url, $user->getProfileUrl());

        $this->assertEquals($data, $user->toArray());
    }
}
