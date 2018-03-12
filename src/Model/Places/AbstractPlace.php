<?php
/**
 * Created by PhpStorm.
 * User: ross
 * Date: 3/12/18
 * Time: 1:59 AM
 */

namespace BoardingCardSort\Model\Places;

/**
 * Class AbstractPlace
 * @package BoardingCardSort\Model\Places
 * @author Ross Ivantsiv <ross@proofpilot.com>
 */
abstract class AbstractPlace
{
    /** @var  string */
    protected $name;

    /**
     * AbstractPlace constructor.
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

}
