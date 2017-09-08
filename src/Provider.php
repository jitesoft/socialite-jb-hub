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

    const IDENTIFIER    = "JETBRAINS_HUB";
    const PROVIDER_NAME = "jetbrains-hub";

    protected $stateless      = true;
    protected $scopeSeparator = ' ';
    protected $scopes         = [
        "0-0-0-0-0"
    ];

    private static $endpoints = [
        "auth"  => "oauth2/auth",
        "token" => "oauth2/token",
        "user"  => "users/me"
    ];

    /** @var string BaseURL/api/rest/Endpoint */
    private static $urlPattern = "%s/api/rest/%s";

    /**
     * Get the access token response for the given code.
     *
     * @param  string $code
     * @return array
     */
    public function getAccessTokenResponse($code) {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            "form_params" => $this->getTokenFields($code),
            'headers'     => [
                'Accept'        => 'application/json',
                "Authorization" => "Basic " . base64_encode($this->clientId . ":" . $this->clientSecret)
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Get the additional configuration keys this provider requires.
     * @return array|string[]
     */
    public static function additionalConfigKeys() : array {
        return [ 'base_url' ];
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

        $response = $this->getHttpClient()->get(
            $this->buildUrl("user"),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
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
            'id'       => array_get($user, 'id'),
            'name'     => array_get($user, 'name'),
            'email'    => array_get($user, 'profile.email.email'),
            'verified' => array_get($user, 'profile.email.verified'),
            'avatar'   => array_get($user, 'avatar.url'),
            'roles'    => array_get($user, 'projectRoles'),
            'banned'   => array_get($user, 'banned'),
            'guest'    => array_get($user, 'guest')
        ]);
    }

    /**
     * Build url by endpoint type.
     *
     * @param string $endpointType
     * @return string
     * @see self::$endpoints
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
     * Get the POST fields for the token request.
     *
     * @param  string $code
     * @return array
     */
    protected function getTokenFields($code) {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code', 'access_type'=>'offline'
        ]);
    }
}
