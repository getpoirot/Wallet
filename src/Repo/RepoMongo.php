<?php
namespace Poirot\Wallet\Repo;

use Poirot\Wallet\Entity\EntityWallet;
use Poirot\Wallet\Interfaces\iRepoWallet;
use MongoDB\BSON\ObjectID;
use Module\MongoDriver\Model\Repository\aRepository;
use Poirot\Wallet\Repo\Mongo\WalletEntity;


class RepoMongo
    extends aRepository
    implements iRepoWallet
{
    protected $typeMap = [
        'root'     => 'MongoDB\Model\BSONDocument',
        'array'    => 'array',
        'document' => 'array',
    ];

    /**
     * Initialize Object
     *
     */
    protected function __init()
    {
        if (! $this->persist )
            $this->setModelPersist( new Mongo\WalletEntity );
    }

    /**
     * Generate next unique identifier to persist
     * data with
     *
     * @param null|string $id
     *
     * @return mixed
     * @throws \Exception
     */
    function attainNextIdentifier($id = null)
    {
        try {
            $objectId = ($id !== null) ? new ObjectID( (string)$id ) : new ObjectID;
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Invalid Persist (%s) Id is Given.', $id));
        }

        return $objectId;
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
        $r = $this->_query()->findOne(
            [
                'owner_id'    => $this->attainNextIdentifier($entityWallet->getOwnerId()),
                'wallet_type' => $entityWallet->getWalletType(),
            ]
            , [
                'sort'  => ['_id' => -1,],
            ]
        );

        if ($r)
            $lastTotal = $r->getLastTotal()+$entityWallet->getAmount();
        else
            $lastTotal = $entityWallet->getAmount();


        $pEntity = new Mongo\WalletEntity;
        $pEntity
            ->setOwnerId( $this->attainNextIdentifier($entityWallet->getOwnerId()) )
            ->setWalletType( $entityWallet->getWalletType() )
            ->setAmount( $entityWallet->getAmount() )
            ->setTarget( $entityWallet->getTarget() )
            ->setLastTotal( $lastTotal )
            ->setMeta( $entityWallet->getMeta() )
            ->setDateTimeCreated( $entityWallet->getDateTimeCreated() )
        ;

        $r = $this->_query()->insertOne($pEntity);

        $rEntity = new EntityWallet();
        $rEntity
            ->setOwnerId( $r->getInsertedId() )
            ->setWalletType( $entityWallet->getWalletType() )
            ->setAmount( $entityWallet->getAmount() )
            ->setTarget( $entityWallet->getTarget() )
            ->setLastTotal( $lastTotal )
            ->setMeta( $entityWallet->getMeta() )
            ->setDateTimeCreated( $entityWallet->getDateTimeCreated() )
        ;

        return $rEntity;
    }

    /**
     * Get last amount wallet of any user
     *
     * @param mixed $uid Owner unique id of wallet
     * @param string $walletType Wallet type
     *
     * @return float|int|null
     */
    function getSumTotalAmount($uid, $walletType)
    {

        $r = $this->_query()->findOne(
            [
                'owner_id'    => $this->attainNextIdentifier($uid),
                'wallet_type' => $walletType,
            ]
            , [
                'sort'  => ['_id' => -1,],
            ]
        );

        if (!$r)
            return 0;


        return $r->getLastTotal();
    }

    /**
     * Find Owner Has Targeted On Specified Item
     *
     * @param mixed  $owner
     * @param mixed  $target
     * @param string $walletType
     *
     * @return WalletEntity|null
     */
    function findOneTargeted($owner, $target, $walletType)
    {
        $r = $this->_query()->findOne([
            'owner_id'    => $this->attainNextIdentifier( $owner ),
            'target'      => $target,
            'wallet_type' => $walletType
        ]);


        return $r;
    }

    /**
     * Find All Entities Match With Given Expression
     *
     * $exp: [
     *   'owner_id'         => ..,
     *   'wallet_type' => ..,
     *   'target'      => ...
     * ]
     *
     * @param array $expr
     * @param string $offset
     * @param int $limit
     * @param int|string $sort
     *
     * @return \Poirot\Wallet\Entity\EntityWallet[]
     */
    function find(array $expr, $offset = null, $limit = null, $sort = self::SORT_DESC)
    {
        $expr      = \Module\MongoDriver\parseExpressionFromArray($expr);
        $condition = \Module\MongoDriver\buildMongoConditionFromExpression($expr);

        if ($offset)
            $condition = [
                '_id' => [
                    '$lt' => $this->attainNextIdentifier($offset),
                ]
            ] + $condition;


        $result = $this->_query()->find(
            $condition
            , [
                'limit' => $limit,
                'sort'  => [
                    '_id' => ($sort == self::SORT_DESC) ? -1 : 1,
                ],
            ]
        );


        return $result;
    }
}
