<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OneLogin settings
    |--------------------------------------------------------------------------
    */

    // If 'strict' is TRUE, then the PHP Toolkit will reject unsigned
    // or unencrypted messages if it expects them signed or encrypted
    // Also will reject the messages if not strictly follow the SAML
    // standard: Destination, NameId, Conditions ... are validated too.
    'strict' => true,

    // Enable debug mode (to print errors).
    'debug' => env('APP_DEBUG', false),

    // Set a BaseURL to be used instead of try to guess
    // the BaseURL of the view that process the SAML Message.
    // Ex. http://sp.example.com/
    //     http://example.com/sp/
    # 'baseurl' => env('APP_URL'),

    /*
    |--------------------------------------------------------------------------
    | OneLogin advanced settings
    |--------------------------------------------------------------------------
    */

    // Compression settings
    // Handle if the getRequest/getResponse methods will return the Request/Response deflated.
    // But if we provide a $deflate boolean parameter to the getRequest or getResponse
    // method it will have priority over the compression settings.
    # 'compress' => [
    #     'requests'  => true,
    #     'responses' => true,
    # ],

    'security' => [
        /** signatures and encryptions offered */

        // Indicates that the nameID of the <samlp:logoutRequest> sent by this SP
        // will be encrypted.
        'nameIdEncrypted' => false,

        // Indicates whether the <samlp:AuthnRequest> messages sent by this SP
        // will be signed. (The Metadata of the SP will offer this info).
        'authnRequestsSigned' => false,

        // Indicates whether the <samlp:logoutRequest> messages sent by this SP
        // will be signed.
        'logoutRequestSigned' => false,

        // Indicates whether the <samlp:logoutResponse> messages sent by this SP
        // will be signed.
        'logoutResponseSigned' => false,

        // Sign the Metadata.
        // Possible values are:
        // - FALSE
        // - TRUE (use sp certs)
        // - ['keyFileName' => 'metadata.key', 'certFileName' => 'metadata.crt']
        // - ['x509cert' => '', 'privateKey' => '', 'passphrase' => '']
        'signMetadata' => false,

        /** signatures and encryptions required **/

        // Indicates a requirement for the <samlp:Response>, <samlp:LogoutRequest> and
        // <samlp:LogoutResponse> elements received by this SP to be signed.
        'wantMessagesSigned' => false,

        // Indicates a requirement for the <saml:Assertion> elements received by
        // this SP to be encrypted.
        'wantAssertionsEncrypted' => false,

        // Indicates a requirement for the <saml:Assertion> elements received by
        // this SP to be signed. (The Metadata of the SP will offer this info)
        'wantAssertionsSigned' => false,

        // Indicates a requirement for the NameID element on the SAMLResponse received
        // by this SP to be present.
        # 'wantNameId' => true,

        // Indicates a requirement for the NameID received by
        // this SP to be encrypted.
        'wantNameIdEncrypted' => false,

        // Authentication context.
        // Set to FALSE and no AuthContext will be sent in the AuthNRequest,
        // Set TRUE or don't present this parameter and you will get an AuthContext 'exact' 'urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport'
        // Set an array with the possible auth context values: ['urn:oasis:names:tc:SAML:2.0:ac:classes:Password', 'urn:oasis:names:tc:SAML:2.0:ac:classes:X509'].
        'requestedAuthnContext' => false,

        // Allows the authn comparison parameter to be set, defaults to 'exact' if
        // the setting is not present.
        # 'requestedAuthnContextComparison' => 'exact',

        // Indicates if the SP will validate all received xmls.
        // (In order to validate the xml, 'strict' and 'wantXMLValidation' must be true).
        # 'wantXMLValidation' => true,

        // If true, SAMLResponses with an empty value at its Destination
        // attribute will not be rejected for this fact.
        # 'relaxDestinationValidation' => false,

        // Algorithm that the toolkit will use on signing process. Options:
        //    'http://www.w3.org/2000/09/xmldsig#rsa-sha1'
        //    'http://www.w3.org/2000/09/xmldsig#dsa-sha1'
        //    'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256'
        //    'http://www.w3.org/2001/04/xmldsig-more#rsa-sha384'
        //    'http://www.w3.org/2001/04/xmldsig-more#rsa-sha512'
        // Notice that rsa-sha1 is a deprecated algorithm and should not be used.
        # 'signatureAlgorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',

        // Algorithm that the toolkit will use on digest process. Options:
        //    'http://www.w3.org/2000/09/xmldsig#sha1'
        //    'http://www.w3.org/2001/04/xmlenc#sha256'
        //    'http://www.w3.org/2001/04/xmldsig-more#sha384'
        //    'http://www.w3.org/2001/04/xmlenc#sha512'
        // Notice that sha1 is a deprecated algorithm and should not be used.
        # 'digestAlgorithm' => 'http://www.w3.org/2001/04/xmlenc#sha256',

        // ADFS URL-Encodes SAML data as lowercase, and the toolkit by default uses
        // uppercase. Turn it TRUE for ADFS compatibility on signature verification.
        # 'lowercaseUrlencoding' => false,
    ],

    // Contact information template, it is recommended to suply a technical and support contacts.
    'contactPerson' => [
        'technical' => [
            'givenName'    => 'John Doe',
            'emailAddress' => 'john.doe@example.com',
        ],
        'support'   => [
            'givenName'    => 'Jane Doe',
            'emailAddress' => 'jane.doe@example.com',
        ],
    ],

    // Organization information template, the info in 'en' or 'en_US' lang is recomended, add more if required.
    'organization' => [
        'en' => [
            'name'        => 'Name',
            'displayname' => 'Display Name',
            'url'         => 'http://example.com',
        ],
    ],

    /*
     * Interoperable SAML 2.0 Web Browser SSO Profile [saml2int]   http://saml2int.org/profile/current
     *
     * 'authnRequestsSigned' => false,    // SP SHOULD NOT sign the <samlp:AuthnRequest>,
     *                                    // MUST NOT assume that the IdP validates the sign
     * 'wantAssertionsSigned' => true,
     * 'wantAssertionsEncrypted' => true, // MUST be enabled if SSL/HTTPs is disabled
     * 'wantNameIdEncrypted' => false,
     */

];
