<?php
/**
 * Created by PhpStorm.
 * User: ross
 * Date: 3/11/18
 * Time: 4:40 PM
 */

namespace BoardingCardSort\Model\Cards;

use BoardingCardSort\Model\Places\AbstractPlace;
use BoardingCardSort\Model\Places\Airport;

/**
 * Class BusBoardingCard
 * @package AppBundle\Model\Cards
 * @author Ross Ivantsiv <ross@proofpilot.com>
 */
class BusBoardingCard extends AbstractBoardingCard
{

    /** @var  string */
    protected $busNumber;

    /**
     * BusBoardingCard constructor.
     * @param AbstractPlace $origin
     * @param AbstractPlace $destination
     * @param string $busNumber
     * @param string $seat
     */
    public function __construct(AbstractPlace $origin, AbstractPlace $destination, $busNumber, $seat = null)
    {
        parent::__construct($origin, $destination, $seat);
        $this->busNumber = $busNumber;
    }


    /**
     * Checks if bus has seat assignment
     * @return bool
     */
    public function hasSeatAssignment(): boolean
    {
        return $this->getSeat() ? true : false;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->origin instanceof Airport || $this->destination instanceof Airport) {
            $str = "Take the airport bus from {$this->origin} to {$this->destination}. No seat assignment.";
        } else {
            $str = "Take the bus from {$this->origin} to {$this->destination}. Seat {$this->seat}.";
        }

        return $str;
    }
}
