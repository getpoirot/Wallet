<?php
namespace Poirot\Wallet;

use Poirot\Wallet\Interfaces\iRepoWallet;


class RepoMysqlPdo
    implements iRepoWallet
{
    /** @var \PDO */
    private $conn;


    /**
     * RepoWallet constructor.
     *
     * @param \PDO $connection
     */
    function __construct(\PDO $connection)
    {
        $this->conn = $connection;
    }

    /**
     * Get last amount wallet of any user
     *
     * @param mixed $uid
     * @param string $type
     *
     * @return float|int
     */
    function getCountTotalAmount($uid, $type)
    {

    }

    /**
     * Persist Entity Object
     *
     * @param EntityWallet $entityWallet
     *
     * @return mixed UID
     */
    function insert(EntityWallet $entityWallet)
    {
        // TODO: Implement insert() method.
    }

    /**
     * Find All Entities Match With Given Expression
     *
     * @param array $expr
     * @param string $offset
     * @param int $limit
     * @param string $sort
     *
     * @return \Traversable
     */
    function find(array $expr, $offset = null, $limit = null, $sort = self::SORT_ASC)
    {
        // TODO: Implement find() method.
    }
}
