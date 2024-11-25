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
        // Ajout du champ "color" dans les champs gérés par le formulaire
        $this->setFields(array('id', 'name', 'info', 'color'));
    }

    public function save()
    {
        // Position automatique si l'entrée est nouvelle
        if ($this->isNew()) {
            $last = \BooklyLocations\Lib\Entities\Location::query()->select('MAX(position) AS position')->fetchRow();
            $this->object->setPosition($last['position'] + 1);
        }

        return parent::save();
    }
}
