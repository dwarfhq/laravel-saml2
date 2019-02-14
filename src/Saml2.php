<?php

namespace Aacotroneo\Saml2;

use Illuminate\Support\Facades\Cookie;
use OneLogin\Saml2\Auth;
use OneLogin\Saml2\Error;
use OneLogin\Saml2\Utils;
use Aacotroneo\Saml2\Events\LoginEvent;
use Aacotroneo\Saml2\Events\LogoutEvent;
use Aacotroneo\Saml2\Exceptions\Exception;
use Aacotroneo\Saml2\Models\User;

class Saml2
{
    /**
     * Config instance.
     *
     * @var \Aacotroneo\Saml2\Config
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param \Aacotroneo\Saml2\Config $config
     *
     * @return void
     */
    public function __construct(Config $config)
    {
        $this->config = $config;

        // Should we enable proxy vars?
        if ($this->config->proxy_vars) {
            Utils::setProxyVars(true);
        }
    }

    /**
     * Get config instance.
     *
     * @return \Aacotroneo\Saml2\Config
     */
    public function config(): Config
    {
        return $this->config;
    }

    /**
     * Process Assertion Consumer Services response from Identity Provider.
     *
     * @param string|null $slug      Service Provider slug.
     * @param string|null $requestId The ID of the AuthNRequest sent by this SP to the IdP.
     *
     * @throws \Aacotroneo\Saml2\Exceptions\Exception On any error.
     *
     * @return \Aacotroneo\Saml2\Models\User
     */
    public function acs(string $slug = null, string $requestId = null): User
    {
        $auth = $this->loadAuth($slug);
        $auth->processResponse($requestId);
        $errorException = $auth->getLastErrorException();
        if (!empty($errorException)) {
            throw new Exception($auth->getLastErrorReason(), 0, $errorException);
        }

        $user = new User($auth);
        event(new LoginEvent($this->config->resolveOneLoginSlug($slug), $user));

        return $user;
    }

    /**
     * Initiate the Single Sign-On process.
     *
     * @param string|null $slug            Service Provider slug.
     * @param string|null $returnTo        The target URL the user should be returned to after login.
     * @param array       $parameters      Extra parameters to be added to the GET request.
     * @param bool        $forceAuthn      When TRUE the AuthNRequest will set the ForceAuthn='true'.
     * @param bool        $isPassive       When TRUE the AuthNRequest will set the IsPassive='true'.
     * @param bool        $stay            TRUE if we want to stay (returns the URL string), FALSE to redirect.
     * @param bool        $setNameIdPolicy When TRUE the AuthNRequest will set a NameIDPolicy element.
     *
     * @return string|null If $stay is TRUE, a string with the SSO URL + LogoutRequest + parameters is returned instead.
     */
    public function login(
        string $slug = null,
        string $returnTo = null,
        array $parameters = [],
        bool $forceAuthn = false,
        bool $isPassive = false,
        bool $stay = false,
        bool $setNameIdPolicy = true
    ): ?string {
        return $this->loadAuth($slug)->login(
            $returnTo,
            $parameters,
            $forceAuthn,
            $isPassive,
            $stay,
            $setNameIdPolicy
        );
    }

    /**
     * Initiate the Single Logout process.
     *
     * @param string|null $slug                  Service Provider slug.
     * @param string|null $returnTo              The target URL the user should be returned to after logout.
     * @param array       $parameters            Extra parameters to be added to the GET.
     * @param string|null $nameId                The NameID that will be set in the LogoutRequest.
     * @param string|null $sessionIndex          The SessionIndex (taken from the SAML Response in the SSO process).
     * @param bool        $stay                  TRUE if we want to stay (returns the URL string), FALSE to redirect.
     * @param string|null $nameIdFormat          The NameID Format will be set in the LogoutRequest.
     * @param string|null $nameIdNameQualifier   The NameID NameQualifier will be set in the LogoutRequest.
     * @param string|null $nameIdSPNameQualifier The NameID SP NameQualifier will be set in the LogoutRequest.
     *
     * @throws \OneLogin\Saml2\Error If Identity Provider doesn't support Single Logout.
     *
     * @return string|null If $stay is TRUE, a string with the SLO URL + LogoutRequest + parameters is returned instead.
     */
    public function logout(
        string $slug = null,
        string $returnTo = null,
        array $parameters = [],
        string $nameId = null,
        string $sessionIndex = null,
        bool $stay = false,
        string $nameIdFormat = null,
        string $nameIdNameQualifier = null,
        string $nameIdSPNameQualifier = null
    ): ?string {
        return $this->loadAuth($slug)->logout(
            $returnTo,
            $parameters,
            $nameId,
            $sessionIndex,
            $stay,
            $nameIdFormat,
            $nameIdNameQualifier,
            $nameIdSPNameQualifier
        );
    }

    /**
     * Get metadata for the specified Service Provider.
     *
     * @param string|null $slug Service Provider slug.
     *
     * @throws \OneLogin\Saml2\Error If metadata validation fails.
     *
     * @return string Metadata XML string.
     */
    public function metadata(string $slug = null): string
    {
        $settings = $this->loadAuth($slug)->getSettings();
        $metadata = $settings->getSPMetadata();
        $errors = $settings->validateMetadata($metadata);

        if (empty($errors)) {
            return $metadata;
        }

        throw new Error('Invalid Service Provider metadata: %s', Error::METADATA_SP_INVALID, [implode(', ', $errors)]);
    }

    /**
     * Process Single Logout request/response.
     *
     * @param  string|null $slug             Service Provider slug.
     * @param  bool        $keepLocalSession When FALSE will destroy the local session, otherwise will keep it
     * @param  string|null $requestId        The ID of the LogoutRequest sent by this SP to the IdP.
     * @param  bool        $paramsFromServer TRUE if we want to use parameters from $_SERVER to validate the signature.
     * @param  bool        $stay             TRUE if we want to stay (returns the URL string), FALSE to redirect.
     *
     * @throws \OneLogin\Saml2\Error                  If SAML LogoutRequest/LogoutResponse wasn't found.
     * @throws \Aacotroneo\Saml2\Exceptions\Exception On any other error.
     *
     * @return string|null If $stay is TRUE, a string with the SLO URL + LogoutRequest + parameters is returned instead.
     */
    public function sls(
        string $slug = null,
        bool $keepLocalSession = false,
        string $requestId = null,
        bool $paramsFromServer = false,
        bool $stay = false
    ): ?string {
        $callback = function () use ($slug) {
            event(new LogoutEvent($this->config->resolveOneLoginSlug($slug)));
        };
        $auth = $this->loadAuth($slug);
        $url = $auth->processSLO($keepLocalSession, $requestId, $paramsFromServer, $callback, $stay);
        $errorException = $auth->getLastErrorException();
        if (!empty($errorException)) {
            throw new Exception($auth->getLastErrorReason(), 0, $errorException);
        }

        return $url;
    }

    /**
     * Load OneLogin Auth instance for a specific Service Provider.
     *
     * @param string|null $slug Service Provider slug.
     *
     * @return \OneLogin\Saml2\Auth
     */
    public function loadAuth(string $slug = null): Auth
    {
        return new Auth($this->config->getOneLogin($slug));
    }

    /**
     * Set cookie.
     *
     * @param string|null $slug    Service Provider slug.
     * @param array       $data    Serializable data.
     * @param int         $minutes Cookie lifetime.
     *
     * @return void
     */
    public function setCookie(?string $slug, array $data, int $minutes = 0): void
    {
        Cookie::queue($this->resolveCookieName($slug), serialize($data), $minutes);
    }

    /**
     * Get cookie - optionally a specific key if provided.
     *
     * @param string|null $slug Service Provider slug.
     * @param string|null $key  Data key to retrieve.
     *
     * @return mixed
     */
    public function getCookie(?string $slug, string $key = null)
    {
        $cookie = Cookie::get($this->resolveCookieName($slug));
        $data = is_string($cookie) ? unserialize($cookie) : [];

        return $key ? ($data[$key] ?? null) : $data;
    }

    /**
     * Forget cookie.
     *
     * @param stringünull $slug Service Provider slug.
     *
     * @return void
     */
    public function forgetCookie(?string $slug): void
    {
        Cookie::forget($this->resolveCookieName($slug));
    }

    /**
     * Resolve cookie name from Service Provider slug.
     *
     * @param string|null $slug Service Provider slug.
     *
     * @return string
     */
    public function resolveCookieName(?string $slug): string
    {
        return 'saml2_' . ($this->config->resolveOneLoginSlug($slug) ?: 'default');
    }
}
