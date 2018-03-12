<?php
/**
 * Created by PhpStorm.
 * User: ross
 * Date: 3/11/18
 * Time: 7:22 PM
 */

namespace BoardingCardSort\Service;

use BoardingCardSort\Model\Cards\AbstractBoardingCard;
use BoardingCardSort\Model\Places\AbstractPlace;

/**
 * Class PathFinder
 * @package BoardingCardSort\Service
 * @author Ross Ivantsiv <ross@proofpilot.com>
 */
class PathFinder extends Singleton
{
    /**
     * @param AbstractBoardingCard[] $cards
     * @return AbstractBoardingCard[]
     */
    public function getPath(array $cards): array
    {
        $sorted = $temp = [];

        foreach ($cards as $card) {
            $origin = $card->getOrigin();
            if (!$this->getInBoardingCard($origin, $cards)) {
                $temp[] = $card;
            }
        }

        while (!empty($temp)) {
            /** @var AbstractBoardingCard $startingCard */
            $sorted[] = $startingCard = array_shift($temp);

            $destination = $startingCard->getDestination();
            /** @var AbstractBoardingCard $card */
            if ($card = $this->getOutBoardingCardUnvisited($destination, $cards)) {
                $card->setVisited(true);

                $endPlace = $card->getDestination();
                if (!$this->getInBoardingCardUnvisited($endPlace, $cards)) {
                    $temp[] = $card;
                }
            }
        }

        return $sorted;


    }


    /**
     * @param AbstractPlace $destination
     * @param AbstractBoardingCard[] $cards
     * @return AbstractBoardingCard|null
     */
    private function getInBoardingCard(AbstractPlace $destination, array $cards)
    {
        $card = reset(array_filter(
            $cards,
            function ($card) use ($destination) {
                return $card->getDestination()->getName() === $destination->getName() ? true : false;
            }
        ));

        return $card ?: null;
    }

    /**
     * @param AbstractPlace $destination
     * @param AbstractBoardingCard[] $cards
     * @return AbstractBoardingCard|null
     */
    private function getInBoardingCardUnvisited(AbstractPlace $destination, array $cards)
    {
        $card =  reset(array_filter(
            $cards,
            function ($card) use ($destination) {
                return ($card->getDestination()->getName() === $destination->getName()) && (!$card->isVisited()) ? true : false;
            }
        ));

        return $card ?: null;
    }

    /**
     * @param AbstractPlace $origin
     * @param AbstractBoardingCard[] $cards
     * @return AbstractBoardingCard|null
     */
    private function getOutBoardingCardUnvisited(AbstractPlace $origin, array $cards)
    {
        $card =  reset(array_filter(
            $cards,
            function ($card) use ($origin) {
                return ($card->getOrigin()->getName() === $origin->getName()) && (!$card->isVisited()) ? true : false;
            }
        ));

        return $card ?: null;
    }
}
