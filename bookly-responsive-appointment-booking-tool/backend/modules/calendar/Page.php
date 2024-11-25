<?php
namespace Bookly\Backend\Modules\Calendar;

use Bookly\Lib;
use Bookly\Lib\Config;
use Bookly\Lib\Entities\CustomerAppointment;
use Bookly\Lib\Entities\Staff;
use Bookly\Lib\Utils\Common;
use Bookly\Lib\Utils\DateTime;
use Bookly\Lib\Utils\Price;

class Page extends Lib\Base\Ajax
{
    /**
     * Render page.
     */
    public static function render()
    {
        // Code existant (inchangé)
    }

    /**
     * Build appointments for Event Calendar.
     *
     * @param Lib\Query $query
     * @param string $display_tz
     * @return array
     */
    public static function buildAppointmentsForCalendar(Lib\Query $query, $display_tz)
    {
        // Code existant pour les variables et les templates

        $query = self::getAppointmentsForCalendar($query);

        $appointments = array();
        $wp_tz = Config::getWPTimeZone();
        $convert_tz = $display_tz !== $wp_tz;

        foreach ($query as $appointment) {
            if (!isset($appointments[$appointment['id']])) {
                if ($convert_tz) {
                    $appointment['start_date'] = DateTime::convertTimeZone($appointment['start_date'], $wp_tz, $display_tz);
                    $appointment['end_date'] = DateTime::convertTimeZone($appointment['end_date'], $wp_tz, $display_tz);
                }
                $appointments[$appointment['id']] = $appointment;
            }
            // Gestion des clients
            $appointments[$appointment['id']]['customers'][] = array(
                'appointment_id' => $appointment['id'],
                'status' => $appointment['status'],
                'number_of_persons' => $appointment['number_of_persons'],
            );
        }

        // Gestion des couleurs
        $coloring_mode = get_option('bookly_cal_coloring_mode');
        foreach ($appointments as $key => $appointment) {
            $event_color = '';
            switch ($coloring_mode) {
                case 'status':
                    $event_color = Lib\Proxy\Shared::prepareColorsStatuses()[$appointment['status']] ?? '#cccccc';
                    break;
                case 'staff':
                    $event_color = $appointment['staff_color'] ?? '#cccccc';
                    break;
                case 'service':
                    $event_color = $appointment['service_color'] ?? '#cccccc';
                    break;
                case 'location': // Ajout du mode location
                    $event_color = $appointment['location_color'] ?? '#cccccc';
                    break;
                default:
                    $event_color = '#cccccc';
                    break;
            }

            $appointments[$key] = array(
                'id' => $appointment['id'],
                'start' => $appointment['start_date'],
                'end' => $appointment['end_date'],
                'title' => $appointment['service_name'],
                'color' => $event_color,
                'resourceId' => $appointment['staff_id'],
                'extendedProps' => array(
                    'tooltip' => '', // Ajouter un tooltip si nécessaire
                ),
            );
        }

        return array_values($appointments);
    }

    /**
     * Get appointments for the calendar.
     *
     * @param Lib\Query $query
     * @return array
     */
    public static function getAppointmentsForCalendar(Lib\Query $query)
    {
        $query
            ->addSelect(
                'a.id, a.start_date, DATE_ADD(a.end_date, INTERVAL a.extras_duration SECOND) AS end_date,
                COALESCE(s.color,"#cccccc") AS service_color, l.color AS location_color,
                st.color AS staff_color, ca.status'
            )
            ->leftJoin('CustomerAppointment', 'ca', 'ca.appointment_id = a.id')
            ->leftJoin('Service', 's', 's.id = a.service_id')
            ->leftJoin('Location', 'l', 'l.id = a.location_id', '\BooklyLocations\Lib\Entities')
            ->leftJoin('Staff', 'st', 'st.id = a.staff_id')
            ->groupBy('a.id');

        return $query->fetchArray();
    }
}
