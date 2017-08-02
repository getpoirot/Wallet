<?php
namespace Poirot\Wallet;

use MongoDB\BSON\Timestamp;
use Poirot\Std\Struct\aDataOptions;

class EntityWallet
    extends aDataOptions
{
    protected $uid;
    protected $wallet_type;
    protected $amount;
    protected $from;
    protected $dateCreated;
    protected $last_total;


    /**
     * Get User Id That Belong To Wallet
     *
     * @return string
     */
    function getUid()
    {
        return $this->uid;
    }

    /**
     *
     *
     * @param string $uid
     *
     * @return $this
     */
    function setUid($uid)
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * Get Type off any record like dollar toman ....
     * @return string
     */
    function getWalletType()
    {
        return $this->wallet_type;

    }

    /**
     * @param string $wallet_type
     * @return $this
     */
    function setWalletType($wallet_type)
    {
        $this->wallet_type = $wallet_type;
        return $this;
    }

    /**
     * Get amount off any wallet_master
     * @return float
     */
    function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return $this
     */
    function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Get a string that any amount come from where
     * @return string
     */
    function getFrom()
    {
        return $this->from;
    }

    /**
     * @param string $from
     * @return $this
     */
    function setFrom($from)
    {
        $this->from = $from;
        return $this;
    }

    /**
     * Get timestamp of any insert off any record
     * @return Timestamp
     */
    function getDateCreated()
    {
        if (! $this->dateCreated )
            $this->setDateCreated(new \DateTime());


        return $this->dateCreated;
    }

    /**
     * @param \DateTime $dataCreated
     * @return $this
     */
    function setDateCreated($dataCreated)
    {
        $this->dateCreated = $dataCreated;
        return $this;
    }

    /**
     * Get last value for wallet_master off any user
     * @return float
     */
    function getLastTotal()
    {
        return $this->last_total;
    }

    /**
     * @param mixed $last_total
     * @return $this
     */
    function setLastTotal($last_total)
    {
        $this->last_total = $last_total;
        return $this;
    }
	
}