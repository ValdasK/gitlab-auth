<?php

namespace spec\GitlabAuth;

use GuzzleHttp\Client;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AuthSpec extends ObjectBehavior
{
    function let(Client $client)
    {
        $this->beConstructedWith($client, 'http://test.com', 'SECRET_ID', 'SECRET_KEY', 'http://mywebsite.com');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('GitlabAuth\Auth');
    }

    function it_should_create_login_url()
    {
        $this->getLoginRedirectUrl()
            ->shouldBe('http://test.com/oauth/authorize?client_id=SECRET_ID&response_type=code&redirect_uri='.urlencode('http://mywebsite.com'));
    }

    function it_should_allow_extra_parameters_with_overwrite()
    {
        $this->getLoginRedirectUrl(['redirect_uri' => 'b'])
            ->shouldBe('http://test.com/oauth/authorize?client_id=SECRET_ID&response_type=code&redirect_uri=b');
    }

    function it_should_not_allow_empty_code()
    {
        $this->shouldThrow('GitlabAuth\Exception\InvalidCodeException')->duringGetUserByCode('');
    }

    function it_should_ensure_access_token_is_provided()
    {
        $this->shouldThrow('GitlabAuth\Exception\GitlabAuthException')->duringGetTokenContainer();
    }
}
