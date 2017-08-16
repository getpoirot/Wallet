<?php
namespace Poirot\Wallet\Entity;

use Poirot\Std\Struct\aDataOptions;


class EntityWallet
    extends aDataOptions
{
    protected $ownerId;
    protected $walletType;
    protected $amount;
    protected $target;
    protected $dateCreated;
    protected $last_total;
    protected $meta;



    /**
     * Set Owner UID Of Wallet
     *
     * @param string $ownerId
     *
     * @return $this
     */
    function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;
        return $this;
    }

    /**
     * Get Owner Id That Belong To Wallet
     *
     * @return mixed
     */
    function getOwnerId()
    {
        return $this->ownerId;
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
        $this->walletType = (string) $walletType;
        return $this;
    }

    /**
     * Get Type off any record
     * exp. dollar, toman,  ..
     *
     * @return string
     */
    function getWalletType()
    {
        return $this->walletType;

    }

    /**
     * Set Amount
     *
     * @param float $amount Positive or Negative
     *
     * @return $this
     */
    function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Get amount off any wallet_master
     *
     * @return float|int Negative and Positive
     */
    function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set Target
     *
     * @param string $target
     *
     * @return $this
     */
    function setTarget($target)
    {
        $this->target = (string) $target;
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
     * Get a string that include like json ,json,csv, serilize ...
     * that developer know which must be chosen
     * exp (if you buy charge refer to factor )
     *
     * @return string
     */
    public function getMeta()
    {
        return $this->meta;
    }


    /**
     * Set Meta
     *
     * @param string $meta
     *
     * @return $this
     */
    public function setMeta($meta)
    {
        $this->meta = (string)$meta;
        return $this;

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

}
