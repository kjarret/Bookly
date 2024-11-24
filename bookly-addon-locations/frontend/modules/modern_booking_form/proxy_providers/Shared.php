<?php
namespace BooklyLocations\Frontend\Modules\ModernBookingForm\ProxyProviders;

use Bookly\Lib as BooklyLib;
use Bookly\Frontend\Modules\ModernBookingForm\Proxy;
use BooklyLocations\Lib;

class Shared extends Proxy\Shared
{
    /**
     * @inerhitDoc
     */
    public static function prepareAppearance( array $bookly_options )
    {
        $bookly_options['show_locations_filter'] = true;
        $bookly_options['default_location'] = null;
        array_splice( $bookly_options['services_fields_order'], array_search( 'duration', $bookly_options['services_fields_order'], true ), 0, 'location' );
        array_splice( $bookly_options['packages_fields_order'], array_search( 'duration', $bookly_options['packages_fields_order'], true ), 0, 'location' );
        $bookly_options['services_fields_show'][] = 'location';
        $bookly_options['packages_fields_show'][] = 'location';
        $bookly_options['l10n']['location'] = __( 'Location', 'bookly' );
        $bookly_options['l10n']['select_location'] = __( 'Any', 'bookly' );

        return $bookly_options;
    }

    /**
     * @inerhitDoc
     */
    public static function prepareAppearanceData( array $bookly_options )
    {
        $locations = array();
        $rows = Lib\Entities\Location::query( 'l' )->select( 'l.id, l.name' )->sortBy( 'l.position' )->fetchArray();
        foreach ( $rows as $row ) {
            $locations[] = array( 'id' => $row['id'], 'title' => BooklyLib\Utils\Common::getTranslatedString( 'locations_' . $row['id'], $row['name'] ) );
        }

        $bookly_options['locations'] = $locations;
        $bookly_options['services_fields']['location'] = __( 'Location', 'bookly' );
        $bookly_options['packages_fields']['location'] = __( 'Location', 'bookly' );
        $bookly_options['l10n']['service_card']['location'] = __( 'Main Street', 'bookly' );
        $bookly_options['l10n']['package_card']['location'] = __( '2nd Street', 'bookly' );

        return $bookly_options;
    }
}