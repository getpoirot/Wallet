<?php
namespace Poirot\Wallet\Repo;

use Poirot\Wallet\Interfaces\iRepoWallet;
use Poirot\Wallet\Entity\EntityWallet;


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
        $prevSql="SELECT 
    last_total
FROM
    `transactions`
WHERE 
    uid=$entityWallet->getUid
AND 
    wallet_type=$entityWallet->getWalletType

ORDER BY id DESC
LIMIT 1;;";

        $stmt = $this->conn->prepare($prevSql);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $result = $stmt->fetch();
        if (!$result){
            $result=0;
        }
        $result=$result+$entityWallet->getLastTotal();
        var_dump($entityWallet);

        $sql="INSERT INTO transactions (
                 uid, wallet_type, amount, source, data_created,last_total
                )
                VALUES ('{$entityWallet->getUid()}'
                , '{$entityWallet->getWalletType()}'
                , '{$entityWallet->getAmount()}'
                , '{$entityWallet->getSource()}'
                , '{$entityWallet->getDateCreated()->format('Y/m/d H:i:s')}'
                , '{$result}'
                )
                
        ";
        var_dump(2);
        $this->conn->exec($sql);





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