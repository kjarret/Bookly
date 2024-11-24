<?php
namespace BooklyLocations\Backend\Components\Dialogs\Staff\Edit\ProxyProviders;

use Bookly\Lib as BooklyLib;
use Bookly\Backend\Components\Dialogs\Staff\Edit\Proxy;
use BooklyLocations\Lib;

class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function renderStaffDetails( BooklyLib\Entities\Staff $staff )
    {
        $staff_locations = Lib\Entities\StaffLocation::query()
            ->where( 'staff_id', $staff->getId() )
            ->fetchCol( 'DISTINCT location_id' );

        $locations = Lib\Entities\Location::query()
            ->select( 'id, name' )
            ->sortBy( 'position' )
            ->fetchArray();

        self::renderTemplate( 'staff_form', compact( 'locations', 'staff_locations' ) );
    }

    /**
     * @inheritDoc
     */
    public static function updateStaffDetails( BooklyLib\Entities\Staff $staff, array $parameters )
    {
        $location_ids = isset ( $parameters['location_ids'] ) ? $parameters['location_ids'] : array();
        $staff_id = $staff->getId();
        $query = Lib\Entities\StaffLocation::query()->delete()->where( 'staff_id', $staff_id );
        if ( $location_ids ) {
            $query->whereNotIn( 'location_id', $location_ids )->execute();
            $new_locations = Lib\Entities\StaffLocation::query()
                ->where( 'staff_id', $staff_id )
                ->fetchColDiff( 'location_id', $location_ids );
            foreach ( $new_locations as $location_id ) {
                $staff_location = new Lib\Entities\StaffLocation();
                $staff_location->setStaffId( $staff_id )
                    ->setLocationId( $location_id )
                    ->save();
            }
        } else {
            $query->execute();
        }
    }

    /**
     * @inheritDoc
     */
    public static function updateStaffServices( array $_post )
    {
        $location_id = array_key_exists( 'location_id', $_post ) ? $_post['location_id'] : null;
        $staff_id    = $_post['staff_id'];
        if ( $location_id && $staff_id ) {
            $custom_settings = $_post['custom_location_settings'];
            Lib\Entities\StaffLocation::query()
                ->update()
                ->set( 'custom_services', $custom_settings )
                ->where( 'staff_id', $staff_id )
                ->where( 'location_id', $location_id )
                ->execute();
        }
    }

    /**
     * @inheritDoc
     */
    public static function updateStaffSchedule( array $_post )
    {
        $location_id = $_post['location_id'];
        $staff_id = $_post['staff_id'];
        if ( $location_id && $staff_id ) {
            $custom_settings = $_post['custom_location_settings'];
            Lib\Entities\StaffLocation::query()
                ->update()
                ->set( 'custom_schedule', $custom_settings )
                ->where( 'staff_id', $staff_id )
                ->where( 'location_id', $location_id )
                ->execute();
        }
    }

    /**
     * @inheritDoc
     */
    public static function updateStaffSpecialDays( array $_post )
    {
        $location_id = array_key_exists( 'location_id', $_post ) ? $_post['location_id'] : null;
        $staff_id = $_post['staff_id'];
        if ( $location_id && $staff_id ) {
            $custom_settings = $_post['custom_location_settings'];
            Lib\Entities\StaffLocation::query()
                ->update()
                ->set( 'custom_special_days', $custom_settings )
                ->where( 'staff_id', $staff_id )
                ->where( 'location_id', $location_id )
                ->execute();
        }
    }
}