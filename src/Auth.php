<?php
namespace GitlabAuth;

use GitlabAuth\Exception\GitlabAuthException;
use GitlabAuth\Exception\InvalidCodeException;
use GitlabAuth\Exception\InvalidGitlabResponseException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class Auth
{
    /**
     * @var string
     */
    protected $url;
    /**
     * @var string
     */
    protected $appId;
    /**
     * @var string
     */
    protected $appSecret;
    /**
     * @var Client
     */
    protected $client;
    /**
     * @var string
     */
    protected $redirectUrl;
    /**
     * @var
     */
    protected $tokenContainer;

    /**
     * @param Client $client
     * @param string $url
     * @param string $appId
     * @param string $appSecret
     * @param string $redirectUrl
     */
    public function __construct(Client $client, $url, $appId, $appSecret, $redirectUrl)
    {
        $this->client = $client;

        $this->url = $url;
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * Returns the redirect URL where user can authenticate provided application
     *
     * @param array $additionalParams
     * @return string
     */
    public function getLoginRedirectUrl($additionalParams = [])
    {
        return $this->getTrimmedUrl() . '/oauth/authorize?' . $this->getLoginQuery($additionalParams);
    }

    /**
     * @param string $code
     * @return array
     * @throws InvalidCodeException
     * @throws InvalidGitlabResponseException
     */
    public function getUserByCode($code)
    {
        $this->tokenContainer = $this->getAuthTokenByCode($code);

        return $this->client->get($this->getUserInfoUrl())->json();
    }

    public function getTokenContainer()
    {
        if (is_null($this->tokenContainer)) {
            throw new GitlabAuthException('User is not yet authenticated, use getUserByCode first.');
        }

        return $this->tokenContainer;
    }

    /**
     * @param string $code
     * @return array
     * @throws InvalidCodeException
     * @throws InvalidGitlabResponseException
     */
    protected function getAuthTokenByCode($code)
    {
        if (empty($code)) {
            throw new InvalidCodeException('No Gitlab auth code provided. User probably declined authorization.');
        }

        try {
            $auth = $this->client->post($this->getTrimmedUrl() . '/oauth/token', [
                'body' => [
                    'client_id' => $this->appId,
                    'client_secret' => $this->appSecret,
                    'code' => $code,
                    'grant_type' => 'authorization_code',
                    'redirect_uri' => $this->redirectUrl
                ]
            ]);

            $response = $auth->json();

            if ($auth->getStatusCode() == 200 && isset($response['access_token'])) {
                return $response;
            }
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() != 401) {
                throw $e;
            }
        }

        throw new InvalidGitlabResponseException('Provided token has expired or was invalid.');
    }

    /**
     * @param array $additionalParams
     * @return string
     */
    protected function getLoginQuery(array $additionalParams)
    {
        $defaultParams = [
            'client_id' => $this->appId,
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUrl
        ];

        return http_build_query(array_merge($defaultParams, $additionalParams));
    }

    /**
     * @return string
     */
    private function getTrimmedUrl()
    {
        return rtrim($this->url, '/');
    }

    /**
     * @return string
     */
    private function getUserInfoUrl()
    {
        return $this->getTrimmedUrl() . '/api/v3/user?access_token=' . $this->tokenContainer['access_token'];
    }
}
