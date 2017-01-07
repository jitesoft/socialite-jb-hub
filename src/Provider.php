<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  Provider.php - Part of the socialite-jetbrains-hub project.

  © - Jitesoft 2017
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\Socialite\JetbrainsHub;

use SocialiteProviders\Manager\OAuth2\User;
use Laravel\Socialite\Two\ProviderInterface;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;

class Provider extends AbstractProvider implements ProviderInterface {

    const IDENTIFIER = "JETBRAINS_HUB";

    private static $endpoints = [
        "auth" => "oauth2/auth",
        "token" => "oauth2/token",
        "user" => "users/me"
    ];

    /** @var string BaseURL/api/rest/Endpoint */
    private static $urlPattern = "%s/api/rest/%s";

    /**
     * Build url by endpoint type ($endpoint static field).
     *
     * @param string $endpointType
     * @return string
     */
    protected function buildUrl($endpointType) {
        return sprintf(
            self::$urlPattern,
            $this->getConfig("base_url", "http://localhost"),
            self::$endpoints[$endpointType]
        );
    }

    /**
     * Get the authentication URL for the provider.
     *
     * @param  string $state
     * @return string
     */
    protected function getAuthUrl($state) {
        return $this->buildAuthUrlFromBase(
            $this->buildUrl("auth"),
            $state
        );
    }

    /**
     * Get the token URL for the provider.
     *
     * @return string
     */
    protected function getTokenUrl() {
        return $this->buildUrl("token");
    }

    /**
     * Get the raw user for the given access token.
     *
     * @param  string $token
     * @return array
     */
    protected function getUserByToken($token) {
        $response = $this->getHttpClient()->post(
            $this->buildUrl("user"),
            [
                "headers" => [
                    "Accept" => "application/json",
                    "Content-Type" => "application/x-www-form-urlencoded",
                    'Authorization' => 'Bearer '.$token
                ]
            ]
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Map the raw user array to a Socialite User instance.
     *
     * @param  array $user
     * @return \Laravel\Socialite\Two\User
     */
    protected function mapUserToObject(array $user) {

        return (new User())->setRaw($user)->map([
            'id' => array_get($user, 'user_id'),
            'name' => array_get($user, 'name'),
            'email' => array_get($user, 'email'),
            'avatar' => array_get($user, 'picture'),
            'nickname' => array_get($user, 'nickname'),
        ]);
    }

    public static function additionalConfigKeys() {
        return ['base_url'];
    }
}
