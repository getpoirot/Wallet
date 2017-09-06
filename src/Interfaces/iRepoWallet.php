<?php
namespace Poirot\Wallet\Interfaces;

use Poirot\Wallet\Entity\EntityWallet;


interface iRepoWallet
{
    const SORT_ASC  = 'asc';
    const SORT_DESC = 'desc';


    /**
     * Persist Entity Object
     *
     * @param EntityWallet $entityWallet
     *
     * @return EntityWallet entity
     */
    function insert(EntityWallet $entityWallet);

    /**
     * Get last amount wallet of any user
     *
     * @param mixed $uid Owner unique id of wallet
     * @param string $walletType Wallet type
     *
     * @return float|int
     */
    function getSumTotalAmount($uid, $walletType );

    /**
     * Find All Entities Match With Given Expression
     *
     * $exp: [
     *   'uid'         => ..,
     *   'wallet_type' => ..,
     *   'target'      => ...
     * ]
     *
     * @param array   $expr
     * @param string  $offset
     * @param int     $limit
     * @param string  $sort
     *
     * @return \Traversable
     */
    function find(array $expr, $offset = null, $limit = null, $sort = self::SORT_ASC);
}
