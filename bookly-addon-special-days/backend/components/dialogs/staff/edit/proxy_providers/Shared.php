<?php
namespace BooklySpecialDays\Backend\Components\Dialogs\Staff\Edit\ProxyProviders;

use Bookly\Backend\Components\Dialogs\Staff\Edit\Proxy;
use Bookly\Lib as BooklyLib;

class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function renderStaffTab()
    {
        self::renderTemplate( 'tab' );
    }
}