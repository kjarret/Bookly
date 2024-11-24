<?php
namespace BooklyLocations\Backend\Modules\Appearance\ProxyProviders;

use BooklyLocations\Lib;
use Bookly\Backend\Modules\Appearance\Proxy;

class Local extends Proxy\Locations
{
    /**
     * @inheritDoc
     */
    public static function renderLocation()
    {
        self::renderTemplate( 'location' );
    }
}