<?php
namespace BooklyLocations\Backend\Modules\Calendar\ProxyProviders;

use Bookly\Backend\Modules\Calendar\Proxy;
use Bookly\Lib\Query;
use BooklyLocations\Lib;

class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function prepareAppointmentCodesData(array $codes, $appointment_data, $participants)
    {
        $location_id = $appointment_data['location_id'];
        if ($location_id) {
            $location = Lib\Entities\Location::find($location_id);
            if ($location) {
                $codes['location_name'] = $location->getName();
                $codes['location_info'] = $location->getInfo();
                $codes['location_color'] = $location->getColor(); // Ajout de la couleur
            }
        }

        return $codes;
    }

    /**
     * @inheritDoc
     */
    public static function prepareAppointmentsQueryForCalendar(Query $query, \DateTime $start_date, \DateTime $end_date, $location_ids)
    {
        if (!empty($location_ids) && !in_array('all', $location_ids)) {
            $raw_where = array();
            if (in_array('no', $location_ids)) {
                $raw_where[] = 'a.location_id IS NULL';
            }

            $location_ids = array_filter($location_ids, 'is_numeric');
            if (!empty($location_ids)) {
                $raw_where[] = 'a.location_id IN (' . implode(',', $location_ids) . ')';
            }

            if ($raw_where) {
                $query->whereRaw(implode(' OR ', $raw_where), array());
            }
        }

        // Ajouter la couleur des localisations dans les données de la requête
        $query
            ->leftJoin('Location', 'l', 'l.id = a.location_id', '\BooklyLocations\Lib\Entities')
            ->addSelect('l.color AS location_color');
    }
}
