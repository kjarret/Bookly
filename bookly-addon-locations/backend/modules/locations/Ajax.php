<?php
namespace BooklyLocations\Backend\Modules\Locations;

use BooklyLocations\Lib;
use Bookly\Lib as BooklyLib;

class Ajax extends BooklyLib\Base\Ajax
{
    /**
     * Get list of locations.
     */
    public static function getLocations()
    {
        $rows = Lib\Entities\Location::query('l')
            ->select('l.id, l.name, l.info, l.position, COUNT(DISTINCT s.id) AS staff, sl.staff_id')
            ->leftJoin('StaffLocation', 'sl', 'sl.location_id = l.id')
            ->leftJoin('Staff', 's', 's.id = sl.staff_id', '\Bookly\Lib\Entities')
            ->groupBy('l.id')
            ->sortBy('l.position')
            ->fetchArray();

        wp_send_json_success($rows);
    }

    /**
     * Update locations position.
     */
    public static function updateLocationsPosition()
    {
        foreach (self::parameter('locations', array()) as $position => $location_id) {
            Lib\Entities\Location::query()
                ->update()
                ->set('position', $position)
                ->where('id', $location_id)
                ->whereNot('position', $position)
                ->execute();
        }
        wp_send_json_success();
    }

    /**
     * Remove location(s).
     */
    public static function deleteLocations()
    {
        $location_ids = array_map('intval', self::parameter('locations', array()));
        BooklyLib\Proxy\Locations::beforeDelete($location_ids);
        Lib\Entities\Location::query()->delete()->whereIn('id', $location_ids)->execute();
        wp_send_json_success();
    }

    /**
     * Add new location.
     */
    public static function saveLocation()
    {
        $form = new Forms\Location();
        $form->bind(self::parameters());
        $location = $form->save();

        $staff_ids = self::parameter('staff_ids', array());
        if (empty($staff_ids)) {
            Lib\Entities\StaffLocation::query()
                ->delete()
                ->where('location_id', $location->getId())
                ->execute();
        } else {
            Lib\Entities\StaffLocation::query()
                ->delete()
                ->where('location_id', $location->getId())
                ->whereNotIn('staff_id', $staff_ids)
                ->execute();
            $new_staff = Lib\Entities\StaffLocation::query()
                ->where('location_id', $location->getId())
                ->fetchColDiff('staff_id', $staff_ids);
            foreach ($new_staff as $staff_id) {
                $staff_location = new Lib\Entities\StaffLocation();
                $staff_location->setStaffId($staff_id)
                    ->setLocationId($location->getId())
                    ->save();
            }
        }

        $row = Lib\Entities\Location::query('l')
            ->select('l.id, l.name, l.info, l.position, COUNT(DISTINCT s.id) AS staff, s.id AS staff_id')
            ->leftJoin('StaffLocation', 'sl', 'sl.location_id = l.id')
            ->leftJoin('Staff', 's', 's.id = sl.staff_id', '\Bookly\Lib\Entities')
            ->groupBy('l.id')
            ->where('l.id', $location->getId())
            ->fetchRow();

        wp_send_json_success($row);
    }

    public static function getLocationLists()
    {
        wp_send_json_success(array(
            'staff_id' => Lib\Entities\StaffLocation::query()->where('location_id', self::parameter('location_id'))->fetchCol('staff_id'),
        ));
    }

}