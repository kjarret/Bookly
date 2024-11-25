<?php
namespace BooklyLocations\Backend\Modules\Appointments\ProxyProviders;

use Bookly\Backend\Modules\Appointments\Proxy;
use BooklyLocations\Lib\Entities\Location;

class Local extends Proxy\Locations
{
    /**
     * @inheritDoc
     */
    public static function renderFilter()
    {
        $locations = Location::query()
            ->select('id, name, color')
            ->sortBy('position')
            ->fetchArray();

        self::renderTemplate('filter', compact('locations'), true);

    }

}