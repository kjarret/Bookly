<?php
namespace BooklyLocations\Lib;

use BooklyLocations\Backend;
use BooklyLocations\Frontend;

abstract class Plugin extends \Bookly\Lib\Base\Plugin
{
    protected static $prefix;
    protected static $title;
    protected static $version;
    protected static $slug;
    protected static $directory;
    protected static $main_file;
    protected static $basename;
    protected static $text_domain;
    protected static $root_namespace;
    protected static $embedded;

    /**
     * @inheritDoc
     */
    protected static function init()
    {
        // Register proxy methods.
        Backend\Components\Dialogs\Staff\Edit\ProxyProviders\Local::init();
        Backend\Components\Dialogs\Staff\Edit\ProxyProviders\Shared::init();
        Backend\Components\TinyMce\ProxyProviders\Shared::init();
        Backend\Modules\Appearance\ProxyProviders\Local::init();
        Backend\Modules\Appearance\ProxyProviders\Shared::init();
        Backend\Modules\Appointments\ProxyProviders\Local::init();
        Backend\Modules\Calendar\ProxyProviders\Local::init();
        Backend\Modules\Calendar\ProxyProviders\Shared::init();
        Backend\Modules\Notifications\ProxyProviders\Shared::init();
        Backend\Modules\Settings\ProxyProviders\Shared::init();
        Frontend\Modules\Booking\ProxyProviders\Shared::init();
        Frontend\Modules\ModernBookingForm\ProxyProviders\Shared::init();
        Notifications\Assets\Item\ProxyProviders\Shared::init();
        ProxyProviders\Local::init();
        ProxyProviders\Shared::init();
    }

    /**
     * @inheritDoc
     */
    protected static function registerAjax()
    {
        Backend\Modules\Locations\Ajax::init();
    }
}