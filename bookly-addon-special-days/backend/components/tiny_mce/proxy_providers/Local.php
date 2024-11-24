<?php
namespace BooklySpecialDays\Backend\Components\TinyMce\ProxyProviders;

use Bookly\Backend\Components\TinyMce\Proxy\SpecialDays;

class Local extends SpecialDays
{
    /**
     * @inheritDoc
     */
    public static function renderStaffCabinetSettings()
    {
        self::renderTemplate( 'option' );
    }
}