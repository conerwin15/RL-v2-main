<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Define the languages you want exported messages for
    |--------------------------------------------------------------------------
    */

    'locales' => ['en', 'zh', 'ko', 'th', 'vi'],

    /*
    |--------------------------------------------------------------------------
    | Define the target to save the exported messages to
    |--------------------------------------------------------------------------
    |
    | Directory for storing the static files generated when using file storage.
    |
    */

    'storage_path' => public_path('vendor/js-localization/'),

    /*
    |--------------------------------------------------------------------------
    | Define the messages to export
    |--------------------------------------------------------------------------
    |
    | An array containing the keys of the messages you wish to make accessible
    | for the Javascript code.
    | Remember that the number of messages sent to the browser influences the
    | time the website needs to load. So you are encouraged to limit these
    | messages to the minimum you really need.
    |
    | Supports nesting:
    |   [ 'mynamespace' => ['test1', 'test2'] ]
    | for instance will be internally resolved to:
    |   ['mynamespace.test1', 'mynamespace.test2']
    |
    */

    'messages' => [
        'lang.select-region',
        'lang.select-admin',
        'lang.select-dealer',
        'lang.upload-file',
        'lang.only-image-pdf-allowed',
        'lang.select-file',
        'lang.file-uploading',
        'lang.upload',
        'lang.chatbot-title',
        'lang.chatbot-link',
        'lang.preview',
        'lang.remove',
        'lang.course-title',
        'lang.only-zip-file-allowed',
        'lang.course-title',
        'lang.please-select-file',
        'lang.are-you-sure',
        'lang.delete-proceed',
        'lang.delete-user-alert',
        'lang.media-title',
    ],

    /*
    |--------------------------------------------------------------------------
    | Set the keys of config properties you want to use in javascript.
    | Caution: Do not expose any configuration values that should be kept privately!
    |--------------------------------------------------------------------------
    */
    'config' => [
        /*'app.debug'  // example*/
    ],

    /*
    |--------------------------------------------------------------------------
    | Disables the config cache if set to true, so you don't have to
    | run `php artisan js-localization:refresh` each time you change configuration files.
    | Attention: Should not be used in production mode due to decreased performance.
    |--------------------------------------------------------------------------
    */
    'disable_config_cache' => true,

    /*
    |--------------------------------------------------------------------------
    | Whether or not to split up the exported messages.js file into separate
    | lang-{locale}.js files.
    |--------------------------------------------------------------------------
    */
    'split_export_files' => false,
];
