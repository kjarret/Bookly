<?php
namespace BooklyLocations\Lib\Entities;

use Bookly\Lib;

class Location extends Lib\Base\Entity
{
    /** @var  string */
    protected $name;
    /** @var  string */
    protected $info;
    /** @var  int */
    protected $position;

    protected static $table = 'bookly_locations';

    protected static $schema = array(
        'id' => array('format' => '%d'),
        'name' => array('format' => '%s'),
        'info' => array('format' => '%s'),
        'position' => array('format' => '%d', 'sequent' => true),
    );

    /**
     * @param null $locale
     * @return string
     */
    public function getTranslatedName($locale = null)
    {
        return Lib\Utils\Common::getTranslatedString('location_' . $this->getId(), $this->getName(), $locale);
    }

    /**
     * @param null $locale
     * @return string
     */
    public function getTranslatedInfo($locale = null)
    {
        return Lib\Utils\Common::getTranslatedString('location_' . $this->getId() . '_info', $this->getInfo(), $locale);
    }

    /**************************************************************************
     * Entity Fields Getters & Setters                                        *
     **************************************************************************/

    /**
     * Gets name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets info
     *
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Sets info
     *
     * @param string $info
     * @return $this
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Gets position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Sets position
     *
     * @param int $position
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**************************************************************************
     * Overridden Methods                                                     *
     **************************************************************************/

    public function save()
    {
        $return = parent::save();
        if ($this->isLoaded()) {
            // Register string for translate in WPML.
            do_action('wpml_register_single_string', 'bookly', 'location_' . $this->getId(), $this->getName());
            do_action('wpml_register_single_string', 'bookly', 'location_' . $this->getId() . '_info', $this->getInfo());
        }

        return $return;
    }

}