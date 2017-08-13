<?php
namespace Poirot\Wallet;

use Poirot\Wallet\Entity\EntityWallet;
use Poirot\Wallet\Interfaces\iRepoWallet;
use Poirot\Wallet\Interfaces\iWalletManager;


class WalletManager
    implements iWalletManager
{
    /** iRepoWallet */
    protected $repoWallet;


    /**
     * iWalletManager constructor.
     *
     * @param iRepoWallet $repoWallet
     */
    function __construct(iRepoWallet $repoWallet)
    {
        $this->repoWallet = $repoWallet;
    }


    /**
     * InCome For Wallet Owner
     *
     * @param mixed     $ownerID      Affected wallet owner
     * @param int|float $amount
     * @param string    $typeOfWallet Type of wallet
     * @param string    $target       Who or What is the reason of this charge
     *
     * @return $this
     * @throws \Exception
     */
    function income($ownerID, $amount, $typeOfWallet = "default", $target = 'direct')
    {
        if ($amount < 0)
            // Negative Values No Allowed!
            throw new \Exception(
                'Negative Value Not Allowed For Income Method, Use ::outgo instead.'
            );


        $wallet = new EntityWallet;
        $wallet
            ->setOwnerId($ownerID)
            ->setWalletType($typeOfWallet)
            ->setTarget($target)
            ->setAmount($amount)
        ;

        $this->repoWallet->insert($wallet);
        return $this;
    }

    /**
     * OutGo For Wallet Owner
     *
     * @param mixed     $ownerID      Affected wallet owner
     * @param int|float $amount
     * @param string    $typeOfWallet Type of wallet
     * @param string    $target       Who or What is the reason of this charge
     *
     * @return $this
     */
    function outgo($ownerID, $amount, $typeOfWallet = "default", $target = 'direct')
    {
        if ($amount > 0)
            $amount *= -1;


        $wallet = new EntityWallet;
        $wallet
            ->setOwnerId($ownerID)
            ->setWalletType($typeOfWallet)
            ->setTarget($target)
            ->setAmount($amount)
        ;

        $this->repoWallet->insert($wallet);
        return $this;
    }

    /**
     * Get Total Cost Of Wallet Owner
     *
     * @param mixed  $ownerID
     * @param string $typeOfWallet Type of wallet
     *
     * @return float|int Can be negative number
     */
    function getTotal($ownerID, $typeOfWallet = "default")
    {
        return $this->repoWallet->getSumTotalAmount($ownerID, $typeOfWallet);
    }
}
