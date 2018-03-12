<?php
/**
 * Created by PhpStorm.
 * User: ross
 * Date: 3/11/18
 * Time: 6:19 PM
 */

namespace BoardingCardSort\Tests;

use BoardingCardSort\Model\Cards\AbstractBoardingCard;
use BoardingCardSort\Model\Cards\BusBoardingCard;
use BoardingCardSort\Model\Cards\FlightBoardingCard;
use BoardingCardSort\Model\Cards\TrainBoardingCard;
use BoardingCardSort\Model\Places\AbstractPlace;
use BoardingCardSort\Model\Places\Airport;
use BoardingCardSort\Model\Places\RailwayStation;
use BoardingCardSort\Model\Places\Spot;
use BoardingCardSort\Service\Singleton;
use Math\Combinatorics\Permutation;
use Faker\Factory as FakerFactory;

define("MAX_NUMBER_OF_CARDS", 10);
define("NUM_CONNECTED_FLIGHTS", 4);

/**
 * Class CardSetGenerator
 *
 * Generaes an ordered BoardigCard set from fake data
 * @package BoardingCardSort\Tests
 * @author Ross Ivantsiv <ross@proofpilot.com>
 */
class CardSetGenerator extends Singleton
{
    const TEST_CONNECTIONS = [
        "EU" => [
            "Madrid Central Station" => [
                "airports" => [
                    "Barajas international airport",
                    "Cuatro Vientos Airport",
                ],
                "places" => [
                    "Gran Via, Madrid",
                    "Plaza Mayor, Madrid",
                    "Paseo del Prado",
                ],
            ],
            "Barcelona Central Station" => [
                "airports" => [
                    "Barcelona International Airport",
                    "Girona-Costa Brava Airport",
                ],
                "places" => [
                    "Passeig de Gracia, Barcelona",
                    "Mercat de la Boqueria, Barcelona",
                ],
            ],
            "Berlin Central Station" => [
                "airports" => [
                    "Berlin Tegel Airport",
                ],
                "places" => [
                    "Berlin Zoological Garden",
                    "Friedrichstrasse",
                ],
            ],
        ],
        "Nordic" => [
            "Stockholm Central Station" => [
                "airports" => [
                    "Stockholm Arlanda Airport",
                ],
                "places" => [
                    "Stockholm Palace, Stockholm",
                    "Nobel Museum, Sockholm",
                ],
            ],
        ],
        "USA" => [
            "New-York Central Station" => [
                "airports" => [
                    "JFK Airport",
                    "La Guardia Airport",
                ],
                "places" => [
                    "Empire State Building, New York",
                    "Central Park, New York",
                ],
            ],
        ],
        "ME" => [
            "Dubai Central Station" => [
                "airports" => [
                    "Dubai International Airport",
                ],
                "places" => [
                    "Deira, Dubai",
                    "Dubai Mall, Dubai",
                    "Dubai Creek, Dubai",
                ],
            ],
        ],
        "UK" => [
            "London Central Station" => [
                "airports" => [
                    "Heathrow Airport",
                    "Gatwick Airport",
                ],
                "places" => [
                    "Big Ben, London",
                    "Hyde Park, London",
                    "Trafalgar Square, London",
                ],
            ],
        ],
    ];

    /** @var string[]  */
    private $places = [];
    /** @var AbstractBoardingCard[] */
    private $boardingCards = [];

    /** @var  FakerFactory */
    private $faker;

    /**
     * @return AbstractBoardingCard[]
     */
    public function generateBoardingCardSet(): array
    {
        $this->faker =  FakerFactory::create();

        $this->generateBoardingCards();

        echo("\n====================== INITIAL DATA =========================\n");
        echo("Airports:\n");
        $airports = $this->getPlacesByType(Airport::class);
        echo(json_encode($airports, JSON_PRETTY_PRINT));
        echo("\nRailway Stations:\n");
        $railwayStations = $this->getPlacesByType(RailwayStation::class);
        echo(json_encode($railwayStations, JSON_PRETTY_PRINT));
        echo("\nSpots:\n");
        $spots = $this->getPlacesByType(Spot::class);
        echo(json_encode($spots, JSON_PRETTY_PRINT));
        echo("\n\nGenerated ".count($this->boardingCards)." connections");

        return $this->getCardChain();
    }

    /**
     * Get the place by type
     * @param $type
     * @return array
     */
    private function getPlacesByType($type)
    {
        $filtered = array_filter(
            $this->places,
            function ($place) use ($type) {
                return get_class($place) === $type ? true : false;
            }
        );
        $filtered = array_map(
            function ($obj) {
                return (string) $obj;
            },
            $filtered
        );

        return array_values($filtered);
    }


    /**
     * TODO: This function generates test data, nothing to do with search algo
     */
    private function generateBoardingCards(): void
    {
        foreach (self::TEST_CONNECTIONS as $key => $area) {
            $this->generateAreaBoardingCards($area);
        }

        $airports = array_filter($this->places, function ($place) {
            return $place instanceof Airport;
        });
        $flightConnections = Permutation::get($airports, 2);
        foreach ($flightConnections as $flightConnection) {
            $flightNumber = $this->faker->regexify('[A-Z]{2}[0-9]{3}');
            $gate = $this->faker->numberBetween(1, 30);
            $seat = $this->faker->numberBetween(1, 37).$this->faker->regexify('[A-F]{1}');

            $this->boardingCards[] = new FlightBoardingCard(
                $flightConnection[0],
                $flightConnection[1],
                $flightNumber,
                $seat,
                $gate,
                "00000"
            );
        }
    }


    /**
     * TODO: This function generates test data, and  has nothing to do with search algo
     * @param array $cities
     */
    private function generateAreaBoardingCards(array $cities): void
    {
        $this->generateTrainBoardingCards(array_keys($cities));

        $this->places = array_merge(
            $this->places,
            array_map(
                function ($name) {
                    return new RailwayStation($name);
                },
                array_keys($cities)
            )
        );

        foreach ($cities as $cityName => $city) {
            $this->places = array_merge(
                $this->places,
                array_map(
                    function ($name) {
                        return new Airport($name);
                    },
                    $city['airports']
                )
            );
            $this->places  = array_merge(
                $this->places,
                array_map(
                    function ($name) {
                        return new Spot($name);
                    },
                    $city['places']
                )
            );
            $this->generateBusBoardingCards($city['places'], $cityName);
            $this->generateShuttleBoardingCards($city['airports'], $cityName);
        }
    }


    /**
     * TODO: This function generates test data, and  has nothing to do with search algo
     * @param $cities
     */
    private function generateTrainBoardingCards($cities): void
    {
        if (count($cities) > 1) {
            $trainConnections = Permutation::get($cities, 2);
            foreach ($trainConnections as $trainConnection) {
                $trainNumber = $this->faker->regexify('[0-9]{2}[A-Z]{1}');
                $seat = $this->faker->numberBetween(1, 500).$this->faker->regexify('[A-Z]{1}');

                $this->boardingCards[] = new TrainBoardingCard(
                    new RailwayStation($trainConnection[0]),
                    new RailwayStation($trainConnection[1]),
                    $trainNumber,
                    $seat
                );
            }
        }
    }


    /**
     * TODO: This function generates test data, and  has nothing to do with search algo
     * @param array $airports
     * @param string $cityName
     */
    private function generateShuttleBoardingCards(array $airports, string $cityName): void
    {
        foreach ($airports as $airport) {
            $busNumber = $this->faker->regexify('[0-9]{2}[A-Z]{1}');

            $this->boardingCards[] = new BusBoardingCard(
                new Airport($airport),
                new RailwayStation($cityName),
                $busNumber,
                null
            );

            $this->boardingCards[] = new BusBoardingCard(
                new RailwayStation($cityName),
                new Airport($airport),
                $busNumber,
                null
            );
        }
    }

    /**
     * TODO: This function generates test data, and  has nothing to do with search algo
     * @param array $places
     */
    private function generateBusBoardingCards(array $places, string $cityName): void
    {
        $busConnections = Permutation::get($places, 2);

        foreach ($busConnections as $busConnection) {
            $busNumber = $this->faker->regexify('[0-9]{2}[A-Z]{1}');
            $seat = $this->faker->numberBetween(1, 30);

            $this->boardingCards[] = new BusBoardingCard(
                new Spot($busConnection[0]),
                new Spot($busConnection[1]),
                $busNumber,
                $seat
            );
        }

        foreach ($places as $place) {
            $busNumber = $this->faker->regexify('[0-9]{2}[A-Z]{1}');
            $this->boardingCards[] = new BusBoardingCard(
                new RailwayStation($cityName),
                new Spot($place),
                $busNumber,
                $seat
            );
            $this->boardingCards[] = new BusBoardingCard(
                new Spot($place),
                new RailwayStation($cityName),
                $busNumber,
                $seat
            );
        }
    }


    /**
     * Generates REALISTIC boarding cards chain
     * TODO: This function generates test data, and  has nothing to do with search algo
     * @param int $limit
     * @param int $maxConnectedFlightsInSet
     * @return array
     * @throws \Exception
     */
    private function getCardChain(): array
    {
        /** @var string[] $placesChain */
        $placesChain = [];
        /** @var AbstractBoardingCard[] $cardChain */
        $cardChain = [];
        /** @var AbstractBoardingCard $lastCard */
        $lastCard = null;

        //Determine starting spot
        $startingSpots = array_filter(
            $this->places,
            function (AbstractPlace $place) {
                return $place instanceof Spot;
            }
        );
        /** @var AbstractPlace $origin */
        $origin = $this->faker->randomElement($startingSpots);
        $placesChain[] = $origin;

        for ($i = 1; $i <= MAX_NUMBER_OF_CARDS; $i++) {
            $nextCard = $this->getNextBoardingCard($origin, $placesChain, $cardChain);
            if ($nextCard) {
                $cardChain[] = $nextCard;
                $placesChain[] = $nextCard->getDestination();
                $origin = $nextCard->getDestination();
            } else {
                break;
            }
        }

        return $cardChain;
    }

    /**
     * TODO: This function generates test data, and  has nothing to do with search algo
     * @param AbstractPlace $origin
     * @param array $placesChain
     * @param array $cardChain
     * @return AbstractBoardingCard|null
     */
    private function getNextBoardingCard(AbstractPlace $origin, array $placesChain, array $cardChain)
    {
        $lastCard = end($cardChain);
        $filteredCards = array_filter(
            $this->boardingCards,
            function (AbstractBoardingCard $boardingCard) use ($origin, $placesChain, $lastCard, $cardChain) {
                $nextPlaceTypes = [ RailwayStation::class ];

                if ($lastCard &&
                    $lastCard->getOrigin() instanceof Spot &&
                    $lastCard->getDestination() instanceof RailwayStation) {
                    $nextPlaceTypes = [ Airport::class, RailwayStation::class ];
                }

                if ($lastCard &&
                    $lastCard->getOrigin() instanceof RailwayStation &&
                    $lastCard->getDestination() instanceof RailwayStation) {
                    $nextPlaceTypes = [ Airport::class ];
                }

                if ($lastCard &&
                    $lastCard->getOrigin() instanceof RailwayStation &&
                    $lastCard->getDestination() instanceof Airport) {
                    $nextPlaceTypes = [ Airport::class ];
                }

                if ($lastCard &&
                    $lastCard->getOrigin() instanceof Airport &&
                    $lastCard->getDestination() instanceof RailwayStation) {
                    $nextPlaceTypes = [ Spot::class ];
                }

                if ($lastCard &&
                    $lastCard->getOrigin() instanceof Airport &&
                    $lastCard->getDestination() instanceof Airport) {
                    $index = count($cardChain)-NUM_CONNECTED_FLIGHTS;
                    if (isset($cardChain[$index]) && $cardChain[$index] instanceof FlightBoardingCard) {
                        $nextPlaceTypes = [ RailwayStation::class ];
                    } else {
                        $nextPlaceTypes = [ Airport::class ];
                    }
                }

                if ($boardingCard->getOrigin()->getName() !== $origin->getName()) {
                    return false;
                }

                if (in_array($boardingCard->getDestination()->getName(), $placesChain)) {
                    return false;
                }

                if (!in_array(get_class($boardingCard->getDestination()), $nextPlaceTypes)) {
                    return false;
                }

                return true;
            }
        );
        $nextBoardingCard = $this->faker->randomElement($filteredCards);

        if ($nextBoardingCard instanceof FlightBoardingCard) {
            if ($lastCard && !$lastCard instanceof FlightBoardingCard) {
                $nextBoardingCard->setBaggadeDropCounter($this->faker->numberBetween(1, 200));
            }
        }

        //fwrite(STDOUT, "From ".$nextBoardingCard->getOrigin()." to ".$nextBoardingCard->getDestination()."\n");

        return $nextBoardingCard;
    }



}
