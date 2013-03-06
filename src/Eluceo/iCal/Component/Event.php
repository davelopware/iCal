<?php

/*
 * This file is part of the eluceo/iCal package.
 *
 * (c) Markus Poerschke <markus@eluceo.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eluceo\iCal\Component;

use Eluceo\iCal\Component;
use Eluceo\iCal\PropertyBag;
use Eluceo\iCal\Property;

/**
 * Implementation of the EVENT component
 */
class Event extends Component
{
    /**
     * @var string
     */
    protected $uniqueId;

	protected $dtStamp;

    /**
     * @var \DateTime
     */
    protected $dtStart;

    /**
     * @var \DateTime
     */
    protected $dtEnd;

    /**
     * @var boolean
     */
    protected $noTime = false;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $location;

	/**
	 * @var string
	 */
	protected $summary;

	/**
     * If set to true the timezone will be added to the event
     *
     * @var bool
     */
    protected $useTimezone = false;

    /**
     * @var int
     */
    protected $sequence = 0;

    /**
     * @var string
     */
    protected $description;

    function __construct($uniqueId = null)
    {
        if (null == $uniqueId) {
            $uniqueId = uniqid();
        }

        $this->uniqueId = $uniqueId;
    	$this->dtStamp = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'VEVENT';
    }

    /**
     * {@inheritdoc}
     */
    public function buildPropertyBag()
    {
        $this->properties = new PropertyBag;

        // mandatory information
        $this->properties->set('UID', $this->uniqueId);
        $this->properties->add($this->buildDateTimeProperty('DTSTAMP', $this->dtStamp, false));
        $this->properties->add($this->buildDateTimeProperty('DTSTART', $this->dtStart, $this->noTime));
        $this->properties->add($this->buildDateTimeProperty('DTEND', $this->dtEnd, $this->noTime));
        $this->properties->set('SEQUENCE', $this->sequence);

        // optional information
        if (null != $this->url) {
            $this->properties->set('URL', $this->url);
        }

        if (null != $this->location) {
            $this->properties->set('LOCATION', $this->location);
        }

        if (null != $this->summary) {
            $this->properties->set('SUMMARY', $this->summary);
        }

        if (null != $this->description) {
            $this->properties->set('DESCRIPTION', $this->description);
        }

        if( $this->noTime )
            $this->properties->set('X-MICROSOFT-CDO-ALLDAYEVENT', 'TRUE');
    }

    /**
     * Creates a Property based on a DateTime object
     *
     * @param string        $name       The name of the Property
     * @param \DateTime     $dateTime   The DateTime
     * @param bool          $noTime     Indicates if the time will be added
     * @return \Eluceo\iCal\Property
     */
    protected function buildDateTimeProperty($name, \DateTime $dateTime, $noTime = false)
    {
        $dateString = $this->getDateString($dateTime, $noTime);
        $params     = array();

        if ($this->useTimezone) {
            $timeZone       = $dateTime->getTimezone()->getName();
            $params['TZID'] = $timeZone;
        }

        if( $noTime )
            $params['VALUE'] = 'DATE';

        return new Property($name, $dateString, $params);
    }

    /**
     * Returns the date format that can be passed to DateTime::format()
     *
     * @param bool $noTime Indicates if the time will be added
     * @return string
     */
    protected function getDateFormat($noTime = false)
    {
        return $noTime ? 'Ymd' : 'Ymd\THis\Z';
    }

    /**
     * Returns a formatted date string
     *
     * @param \DateTime|null  $dateTime  The DateTime object
     * @param bool            $noTime    Indicates if the time will be added
     * @return mixed
     */
    protected function getDateString(\DateTime $dateTime = null, $noTime = false)
    {
        if (empty($dateTime)) {
            $dateTime = new \DateTime();
        }

        return $dateTime->format($this->getDateFormat($noTime));
    }

    public function setDtEnd($dtEnd)
    {
        $this->dtEnd = $dtEnd;
    }

	public function setDtStart($dtStart)
	{
		$this->dtStart = $dtStart;
	}

	public function setDtStamp($dtStamp)
	{
		$this->dtStamp = $dtStamp;
	}

	public function setLocation($location)
    {
        $this->location = $location;
    }

    public function setNoTime($noTime)
    {
        $this->noTime = $noTime;
    }

    public function setSequence($sequence)
    {
        $this->sequence = $sequence;
    }

    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    public function setUniqueId($uniqueId)
    {
        $this->uniqueId = $uniqueId;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function setUseTimezone($useTimezone)
    {
        $this->useTimezone = $useTimezone;
    }

    public function getUseTimezone()
    {
        return $this->useTimezone;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
