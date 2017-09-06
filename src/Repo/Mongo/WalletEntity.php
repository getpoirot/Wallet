<?php
namespace Poirot\Wallet\Repo\Mongo;

use Module\MongoDriver\Model\tPersistable;
use MongoDB\BSON\Persistable;
use MongoDB\BSON\UTCDatetime;
use \Poirot\Wallet\Entity\EntityWallet;


class WalletEntity
    extends EntityWallet
    implements Persistable
{
    use tPersistable;


    /** @var  \MongoId */
    protected $_id;


    // Mongonize Options

    function set_Id($id)
    {
        $this->_id = $id;
    }

    function get_Id()
    {
        return $this->_id;
    }

    function set__Pclass()
    {
        // Ignore Values
    }

    /**
     * Set Created Date
     *
     * @param UTCDatetime $date
     *
     * @return $this
     */
    function setDatetimeCreatedMongo(UTCDatetime $date)
    {
        $this->setDateCreated($date->toDateTime());
        return $this;
    }

    /**
     * Get Created Date
     * note: persist when serialize
     *
     * @return UTCDatetime
     */
    function getDatetimeCreatedMongo()
    {
        $dateTime = $this->getDatetimeCreated();
        return new UTCDatetime($dateTime->getTimestamp() * 1000);
    }

    /**
     * @override Ignore from persistence
     * @ignore
     *
     * Date Created
     *
     * @return \DateTime
     */
    function getDatetimeCreated()
    {
        return parent::getDateCreated();
    }


    // ...

    /**
     * @inheritdoc
     */
    function bsonUnserialize(array $data)
    {
        if (isset($data['args'])) {
            $args = \Poirot\Std\toArrayObject($data['args']);
            $data['args'] = $args;
        }

        $this->import($data);
    }

}