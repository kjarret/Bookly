<?php
namespace BooklyLocations\Backend\Components\TinyMce\ProxyProviders;

use Bookly\Backend\Components\TinyMce\Proxy;

class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function renderBooklyFormHead()
    {
        self::renderTemplate( 'bookly_form_head' );
    }
}