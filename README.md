# BoardingCardSort

## Installation

1. Clone the repository
2. Run `./install.sh` script. What is does is just `composer install`
3. **You need PHP 7.1 or higher to run the code. 7.0 will not do.**
4. To start the test just run `./run.sh`

## How everything works

Every time you run the test it will execute on another set of data. I use [https://github.com/fzaninotto/Faker](fzaninotto/Faker) 
to generate ordered set of BoardingCards.

1. TestCase generates an ordered set of BoardingCards based on fixed set of places (spots, airports, railway stations). 
There are some restrictions applied, like you cannot go by train from NY to London (but this is not related to the task).
2. After that set is shuffled (output presented as set of shuffled instructions).
3. The test initializes PathFinder service, and calls getPath() method to order the shuffled set.
4. If initial ordered array and calculated ordered array do match, test completes SUCESSFULLY.
5. The new ordered set of instructions is displayed.


## IMPORTANT NOTES

1. Sorting algorithm must be pretty fast. I use Kahn algorithm for sorting directed graphs.
It has complexity of O(V+E) Where V is number of vertices and E number of Edges.

In our simple case vertices are represented by Places/Spots, and Edges are represented by Boarding Cards
(each boarding card is connecting 2 spots).

2. Current realization does not include cycles detection (cycles can happen if you will have "extra"
cards in your shuffled set mentioning same origins/destinations). We assume all cards make one straight journey.
This can be, by the way, extended very quickly, because Kahn algorithm supports cycle detection.


## API DOC

I don't like phpDoc much (latest beta ~3.0.0 still fails to support PHP 7.1 type hinting correctly). So I had to remove
some type hints and use phpDoc 2.9 to generate API documentation.

Documentation resides in `/out` folder.

If you want to check algo pay attention to these classes:

- `BoardingCardSort\Model\Cards\AbstractBoardingCard`
- `BoardingCardSort\Model\Cards\BusBoardingCard`
- `BoardingCardSort\Model\Cards\FlightBoardingCard`
- `BoardingCardSort\Model\Cards\TrainBoardingCard`
- `BoardingCardSort\Service\PathFinder`

If you want to check fake data generator look here:

- `BoardingCardSort\Test\CardSetGenerator`




