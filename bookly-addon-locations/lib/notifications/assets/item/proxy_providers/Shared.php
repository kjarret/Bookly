<?php
namespace BooklyLocations\Lib\Notifications\Assets\Item\ProxyProviders;

use Bookly\Lib\Notifications\Assets\Item\Codes;
use Bookly\Lib\Notifications\Assets\Item\Proxy;
use BooklyLocations\Lib;

abstract class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function prepareCodes( Codes $codes )
    {
        $location = Lib\Entities\Location::find( $codes->getItem()->getLocationId() );
        $codes->location_info = $location ? $location->getTranslatedInfo() : '';
        $codes->location_name = $location ? $location->getTranslatedName() : '';
    }

    /**
     * @inheritDoc
     */
    public static function prepareReplaceCodes( array $replace_codes, Codes $codes, $format )
    {
        $replace_codes['location_name'] = $codes->location_name;
        $replace_codes['location_info'] = $codes->location_info;
        $replace_codes['appointments'][0]['location_name'] = $codes->location_name;

        return $replace_codes;
    }
}