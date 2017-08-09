<?php
namespace Poirot\Wallet\Entity;

use Poirot\Std\Struct\aDataOptions;


class EntityWallet
    extends aDataOptions
{
    protected $uid;
    protected $walletType;
    protected $amount;
    protected $target;
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
     *
     * @return string
     */
    function getWalletType()
    {
        return $this->walletType;

    }

    /**
     * Set Wallet Type
     *
     * @param string $walletType
     *
     * @return $this
     */
    function setWalletType($walletType)
    {
        $this->walletType = $walletType;
        return $this;
    }

    /**
     * Get amount off any wallet_master
     *
     * @return float|int
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
     * Get a string that any amount come from which target
     * exp. (bank, user, gift ...)
     *
     * @return string
     */
    function getTarget()
    {
        return $this->target;
    }

    /**
     * @param string $target
     * @return $this
     */
    function setTarget($target)
    {
        $this->target = $target;
        return $this;
    }

    /**
     * Get timestamp of any insert off any record
     *
     * @return \DateTime
     */
    function getDateCreated()
    {
        if (! $this->dateCreated )
            $this->dateCreated = new \DateTime;


        return $this->dateCreated;
    }

    /**
     * Get last value for wallet_master off any user
     *
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
