<?php
/**
 * Created by PhpStorm.
 * User: ross
 * Date: 3/11/18
 * Time: 6:19 PM
 */

namespace BoardingCardSort\Tests;

use BoardingCardSort\Service\PathFinder;
use PHPUnit\Framework\TestCase;
use Faker\Factory as FakerFactory;

/**
 * Class SortTest
 *
 * Testing the PathFinder Service
 * @package BoardingCardSort\Tests
 * @author Ross Ivantsiv <ross@proofpilot.com>
 */
class SortTest extends TestCase
{
    /** @var  FakerFactory */
    private $faker;
    /** @var  CardSetGenerator */

    private $generator;
    /** @var  PathFinder */
    private $pathFinder;

    private $generatedOrderedSet;

    private $shuffledSet;


    protected function setUp()
    {
        parent::setUp();

        $this->faker =  FakerFactory::create();
        $this->generator = CardSetGenerator::getInstance();
        $this->pathFinder = PathFinder::getInstance();

        $this->generatedOrderedSet = $this->generator->generateBoardingCardSet();
        $this->shuffledSet = $this->faker->shuffle($this->generatedOrderedSet);

        echo "\n\nShuffled List of Cards:\n";
        foreach ($this->shuffledSet as $key => $card) {
            echo ($key+1)."-".$card."\n";
        }

    }

    /**
     * Testing correctness of sort algorithm
     */
    public function testPathFinderMethod()
    {
        $start = microtime(true);

        $sortedSet = $this->pathFinder->getPath($this->shuffledSet);

        echo "\n\nSorted List of Cards:\n";
        foreach ($sortedSet as $key => $card) {
            echo ($key+1)."-".$card."\n";
        }

        $this->assertEquals($this->generatedOrderedSet, $sortedSet);

        $timeElapsedSecs = microtime(true) - $start;
        echo "\nTest executed ".round($timeElapsedSecs*1000, 2)."ms";
    }




}
