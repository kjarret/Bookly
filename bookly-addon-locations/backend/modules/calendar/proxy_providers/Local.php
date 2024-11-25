<?php
namespace BooklyLocations\Backend\Modules\Calendar\ProxyProviders;

use Bookly\Backend\Modules\Calendar\Proxy;
use Bookly\Lib as BooklyLib;
use BooklyLocations\Lib\Entities\Location;

class Local extends Proxy\Locations
{
    /**
     * @inheritDoc
     */
    public static function renderCalendarLocationFilter()
    {
        // Inclure la couleur des localisations dans les données récupérées
        $locations = Location::query('l')
            ->select('l.id, l.name, l.color')
            ->sortBy('l.position')
            ->fetchArray();

        self::renderTemplate('location_filter', array(
            'locations' => $locations,
        ));
    }

    /**
     * @inheritDoc
     */
    public static function prepareCalendarQuery(BooklyLib\Query $query)
    {
        return $query
            ->leftJoin('StaffLocation', 'sl', 'sl.staff_id = st.id AND sl.location_id = a.location_id', '\BooklyLocations\Lib\Entities')
            ->leftJoin('StaffService', 'ss', 'ss.staff_id = a.staff_id AND ss.service_id = a.service_id AND ss.location_id <=> IF(sl.custom_services = 1,a.location_id,null)')
            ->leftJoin('Location', 'loc', 'loc.id = a.location_id', '\BooklyLocations\Lib\Entities') // Joindre les localisations pour accéder à leur couleur
            ->addSelect('loc.color AS location_color'); // Ajouter la couleur des localisations dans les résultats
    }
}
