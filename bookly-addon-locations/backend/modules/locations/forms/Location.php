<?php
namespace BooklyLocations\Backend\Modules\Locations\Forms;

/**
 * @property \BooklyLocations\Lib\Entities\Location $object
 */
class Location extends \Bookly\Lib\Base\Form
{
    protected static $entity_class = 'Location';

    protected static $namespace = '\BooklyLocations\Lib\Entities';

    public function configure()
    {
        $this->setFields( array( 'id', 'name', 'info' ) );
    }

    public function save()
    {
        if ( $this->isNew() ) {
            $last = \BooklyLocations\Lib\Entities\Location::query()->select( 'MAX(position) AS position' )->fetchRow();
            $this->object->setPosition( $last['position'] + 1 );
        }

        return parent::save();
    }

}