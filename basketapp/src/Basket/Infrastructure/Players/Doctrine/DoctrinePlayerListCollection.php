<?php namespace Basket\Infrastructure\Players\Doctrine;

use Basket\Domain\Players\Player;
use Doctrine\Common\Collections\ArrayCollection;

use Basket\Application\DataTransformers\Players\PlayerToArrayDataTransformer as ArrayTransformer;

class DoctrinePlayerListCollection
    extends ArrayCollection
{
    /**
     * Transformer for Players input/output
     *
     * @var ArrayTransformer
     */
    protected $player_transformer;

    /**
     * Undocumented function
     *
     * @param ArrayTransformer $player_transformer
     * @param array $data
     */
    public function __construct(ArrayTransformer $player_transformer, array $data = [])
    {
        $this->player_transformer = $player_transformer;
        parent::__construct($data);
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return array_map( array($this->player_transformer,'createPlayer'), parent::toArray());        
    }

    /**
     * {@inheritDoc}
     */
    public function first()
    {
        $result = parent::first();
        return $this->player_transformer->createPlayer($result);
    }

    /**
     * {@inheritDoc}
     */
    protected function createFrom(array $elements)
    {
        $PlayerElements = array_map( array($this->player_transformer,'createPlayer'), $elements);
        return parent::createFrom($PlayerElements);
    }

    /**
     * {@inheritDoc}
     */
    public function last()
    {
        $result = parent::last();
        return $this->player_transformer->createPlayer($result);
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        $result = parent::current();
        return $this->player_transformer->createPlayer($result);
    }

    public function next()
    {
        $result = parent::current();
        return $this->player_transformer->createPlayer($result);
    }

    /**
     * Removes the player with the given "num"
     */
    public function remove($key)
    {
        $result = parent::remove($key);
        return is_null($result) ? $result : $this->player_transformer->createPlayer($result);
    }

    /**
     * Removes a player fom the collection
     * 
     * @param Player $player
     * @return Player or null
     */
    public function removeElement(Player $player)
    {
        $array_player = $this->player_transformer->transform($player);
        $result = parent::removeElement($array_player);
        return is_null($result) ? $result : $player;
    }

    /**
     * Reveals if a player is in the collection
     *
     * @param Player $player
     * @return bool
     */
    public function contains(Player $player)
    {
        $array_player = $this->player_transformer->transform($player);
        return parent::contains($array_player);
    }

    /**
     * Gets the index of a player in the collection
     * if player is indexed
     *
     * @param Player $player
     * @return int|false
     */
    public function indexOf(Player $player){        
        return parent::indexOf($this->player_transformer->transform($player));
    }

    /**
     * Gets the player with the given index
     *
     * @param mixed $key
     * @return Player|null
     */
    public function get($key)
    {
        $result = parent::get($key);
        return is_null($result) ? $result : $this->player_transformer->createPlayer($result);
    }

    /**
     * Returns an array with Players
     *
     * @return array
     */
    public function getValues()
    {
        $values = parent::getValues();
        $result = array_map(array($this->player_transformer,'createPlayer'), $values);
        return $result;
    }

    /**
     * Sets a player in the collection
     * placed at the given $key position
     *
     * @param mixed $key
     * @param Player $player
     * @return void
     */
    public function set($key, Player $player){
        if ($player->num()->asScalar() != $key)
            throw new \InvalidArgumentException(sprintf("\$key must contain \$player num. Expected %s, given %s",$player->num()->asScalar(),$key));
        
        $array_player = $this->player_transformer->transform($player);
        parent::set($key,$array_player);
    }

    /**
     * Adds a player into the collection or Updates it if contained
     *
     * @param Player $player
     * @return true
     */
    public function add(Player $player)
    {
        $array_player = $this->player_transformer->transform($player);        
        return parent::set($player->num,$array_player);
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {        
        $result = array_map(array($this->player_transformer,'createPlayer'), $this->getValues());
        return new \ArrayIterator($result);
    }

}