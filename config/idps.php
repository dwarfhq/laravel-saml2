<?php

use OneLogin\Saml2\Constants;

return [

    /*
    |--------------------------------------------------------------------------
    | Identity Providers
    |--------------------------------------------------------------------------
    */

   'test' => [
       // IdP name (must be the same as the related SP).
       'default' => [
            // Identifier of the IdP entity (must be a URI).
            'entityId' => '',
            // SSO endpoint info of the IdP. (Authentication Request protocol).
            'singleSignOnService' => [
                // URL Target of the IdP where the SP will send the Authentication Request Message.
                'url' => '',
                // SAML protocol binding to be used when returning the <Response> message.
                // Onelogin Toolkit supports for this endpoint the HTTP-Redirect binding only.
                # 'binding' => Constants::BINDING_HTTP_REDIRECT,
            ],
            // SLO endpoint info of the IdP.
            'singleLogoutService' => [
                // URL Location of the IdP where the SP will send the SLO Request.
                'url' => '',
                // SAML protocol binding to be used when returning the <Response> message.
                // Onelogin Toolkit supports for this endpoint the HTTP-Redirect binding only.
                # 'binding' => Constants::BINDING_HTTP_REDIRECT,
                // URL location of the IdP where the SP SLO Response will be sent (ResponseLocation)
                // if not set, url for the SLO Request will be used
                # 'responseUrl' => '',
            ],
            // Public x509 certificate of the IdP.
            'x509cert' => storage_path('certs/test/idp.crt'),
            //  Instead of use the whole x509cert you can use a fingerprint in
            //  order to validate the SAMLResponse, but we don't recommend to use
            //  that method on production since is exploitable by a collision attack.
            //  (openssl x509 -noout -fingerprint -in "idp.crt" to generate it,
            //   or add for example the -sha256 , -sha384 or -sha512 parameter)
            //
            //  If a fingerprint is provided, then the certFingerprintAlgorithm is required in order to
            //  let the toolkit know which Algorithm was used. Possible values: sha1, sha256, sha384 or sha512
            //  'sha1' is the default value.
            # 'certFingerprint'          => '',
            # 'certFingerprintAlgorithm' => 'sha1',

            // In some scenarios the IdP uses different certificates for
            // signing/encryption, or is under key rollover phase and more
            // than one certificate is published on IdP metadata.
            // In order to handle that the toolkit offers this parameter.
            // (when used, 'x509cert' and 'certFingerprint' values are ignored).
            # 'x509certMulti' => [
            #     'signing' => [
            #         storage_path('certs/test/idp-signing.crt'),
            #     ],
            #     'encryption' => [
            #         storage_path('certs/test/idp-encryption.crt'),
            #     ],
            # ],
       ],
   ],

   'prod' => [
       // IdP name (must be the same as the related SP).
       'default' => [
            // Identifier of the IdP entity (must be a URI).
            'entityId' => '',
            // SSO endpoint info of the IdP. (Authentication Request protocol).
            'singleSignOnService' => [
                // URL Target of the IdP where the SP will send the Authentication Request Message.
                'url' => '',
                // SAML protocol binding to be used when returning the <Response> message.
                // Onelogin Toolkit supports for this endpoint the HTTP-Redirect binding only.
                # 'binding' => Constants::BINDING_HTTP_REDIRECT,
            ],
            // SLO endpoint info of the IdP.
            'singleLogoutService' => [
                // URL Location of the IdP where the SP will send the SLO Request.
                'url' => '',
                // SAML protocol binding to be used when returning the <Response> message.
                // Onelogin Toolkit supports for this endpoint the HTTP-Redirect binding only.
                # 'binding' => Constants::BINDING_HTTP_REDIRECT,
                // URL location of the IdP where the SP SLO Response will be sent (ResponseLocation)
                // if not set, url for the SLO Request will be used
                # 'responseUrl' => '',
            ],
            // Public x509 certificate of the IdP.
            'x509cert' => storage_path('certs/prod/idp.crt'),
            //  Instead of use the whole x509cert you can use a fingerprint in
            //  order to validate the SAMLResponse, but we don't recommend to use
            //  that method on production since is exploitable by a collision attack.
            //  (openssl x509 -noout -fingerprint -in "idp.crt" to generate it,
            //   or add for example the -sha256 , -sha384 or -sha512 parameter)
            //
            //  If a fingerprint is provided, then the certFingerprintAlgorithm is required in order to
            //  let the toolkit know which Algorithm was used. Possible values: sha1, sha256, sha384 or sha512
            //  'sha1' is the default value.
            # 'certFingerprint'          => '',
            # 'certFingerprintAlgorithm' => 'sha1',

            // In some scenarios the IdP uses different certificates for
            // signing/encryption, or is under key rollover phase and more
            // than one certificate is published on IdP metadata.
            // In order to handle that the toolkit offers this parameter.
            // (when used, 'x509cert' and 'certFingerprint' values are ignored).
            # 'x509certMulti' => [
            #     'signing' => [
            #         storage_path('certs/prod/idp-signing.crt'),
            #     ],
            #     'encryption' => [
            #         storage_path('certs/prod/idp-encryption.crt'),
            #     ],
            # ],
       ],
   ],

];
