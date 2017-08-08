<?php
namespace Poirot\Wallet\Interfaces;

use Poirot\Wallet\Entity\EntityWallet;


interface iRepoWallet
{
    const SORT_ASC  = 'asc';
    const SORT_DESC = 'desc';


    /**
     * Get last amount wallet of any user
     *
     * @param mixed  $uid
     * @param string $type
     *
     * @return float|int
     */
    function getCountTotalAmount($uid, $type);

    /**
     * Persist Entity Object
     *
     * @param EntityWallet $entityWallet
     *
     * @return mixed UID
     */
    function insert(EntityWallet $entityWallet);

    /**
     * Find All Entities Match With Given Expression
     *
     * @param array   $expr
     * @param string  $offset
     * @param int     $limit
     * @param string  $sort
     *
     * @return \Traversable
     */
    function find(array $expr, $offset=null, $limit=null, $sort = self::SORT_ASC);
}
