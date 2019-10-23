<?php
/**
 * General Configuration
 *
 * All of your system's general configuration settings go in here. You can see a
 * list of the available settings in vendor/craftcms/cms/src/config/GeneralConfig.php.
 *
 * @see craft\config\GeneralConfig
 */

return [
    // Global settings
    '*' => [
        // Default Week Start Day (0 = Sunday, 1 = Monday...)
        'defaultWeekStartDay' => 1,

        // Enable CSRF Protection (recommended)
        'enableCsrfProtection' => false,

        // Whether generated URLs should omit "index.php"
        'omitScriptNameInUrls' => true,

        // Control Panel trigger word
        'cpTrigger' => 'admin',

        // The secure key Craft will use for hashing and encrypting data
        'securityKey' => getenv('SECURITY_KEY'),

        'autoLoginAfterAccountActivation' => false,

        'defaultSearchTermOptions' => [
            'subLeft' => true,
            'subRight' => true,
        ],

        'errorTemplatePrefix' => '_errors/',

        'postLoginRedirect' => 'employee-portal',

        'elevatedSessionDuration' => 86400,

        'generateTransformsBeforePageLoad' => true,

        'rememberedUserSessionDuration' => 31536000,

        'requireMatchingUserAgentForSession' => false,

        'useEmailAsUsername' => true,

        'pageTrigger' => 'page/',

        'userSessionDuration' => false,

        'postLogoutRedirect' => 'login',

        'setPasswordPath' => 'setpassword',

        'setPasswordSuccessPath' => 'login',

        'enableTemplateCaching' => filter_var(getenv('ENABLE_TEMPLATE_CACHING'), FILTER_VALIDATE_BOOLEAN),

        'aliases' => [
            '@assetBaseUrl' => getenv('SITE_URL') . '/uploads',
        ],

        // Base site URL
        'siteUrl' => getenv('SITE_URL'),
    ],

    // Dev environment settings
    'dev' => [
        'devMode' => true,

        'testToEmailAddress' => getenv('TEST_TO_EMAIL_ADDRESS'),

        'aliases' => [
            '@assetBasePath' => CRAFT_BASE_PATH . '/public_html/uploads'
        ],
    ],

    // Staging environment settings
    'staging' => [
        'aliases' => [
            '@assetBasePath' => CRAFT_BASE_PATH . '/uploads',
        ],
    ],

    // Production environment settings
    'production' => [
        'aliases' => [
            '@assetBasePath' => CRAFT_BASE_PATH . '/uploads',
        ],
    ],
];
