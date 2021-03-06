<?php
/**
 * Created by PhpStorm.
 * User: ross
 * Date: 3/11/18
 * Time: 4:40 PM
 */

namespace BoardingCardSort\Model\Cards;

use BoardingCardSort\Model\Places\AbstractPlace;

/**
 * Class TrainBoardingCard
 * @package BoardingCardSort\Model\Cards
 * @author Ross Ivantsiv <ross@proofpilot.com>
 */
class TrainBoardingCard extends AbstractBoardingCard
{
    /** @var  string */
    protected $trainNumber;

    /**
     * TrainBoardingCard constructor.
     * @param AbstractPlace $origin
     * @param AbstractPlace $destination
     * @param string $trainNumber
     * @param string $seat
     */
    public function __construct(AbstractPlace $origin, AbstractPlace $destination, $trainNumber, $seat)
    {
        parent::__construct($origin, $destination, $seat);
        $this->trainNumber = $trainNumber;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $str = "Take train {$this->trainNumber} from {$this->origin} to {$this->destination}. Sit in seat {$this->seat}";

        return $str;
    }
}
