<?php

namespace TriforceRbacBundle\Repository;

use Doctrine\ORM\ORMException;
use TriforceRbacBundle\Entity\NodeInterface;
use TriforceRbacBundle\Entity\Role;
use TriforceRbacBundle\Entity\RoleInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use TriforceRbacBundle\Entity\PermissionInterface;
use TriforceRbacBundle\Core\Manager\NodeManagerInterface;
use TriforceRbacBundle\Exception\RbacRoleNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Role|null find($id, $lockMode = null, $lockVersion = null)
 * @method Role|null findOneBy(array $criteria, array $orderBy = null)
 * @method Role[]    findAll()
 * @method Role[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoleRepository extends ServiceEntityRepository implements NestedSetInterface
{
    use NodeEntityTrait;

    private string $permissionTableName;
    private string $tableName;

    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        parent::__construct($registry, $entityClass);

        $this->permissionTableName = $this->getEntityManager()
            ->getClassMetadata(PermissionInterface::class)
            ->getTableName();
        $this->tableName = $this->getClassMetadata()
            ->getTableName();
    }

    public function initTable()
    {
        $sql = "SET FOREIGN_KEY_CHECKS = 0; TRUNCATE user_role; TRUNCATE role_permission; TRUNCATE {$this->tableName};SET FOREIGN_KEY_CHECKS = 1;";
        $this->getEntityManager()
            ->getConnection()
            ->executeQuery($sql);

        $sql = "INSERT INTO {$this->tableName} (id, code, description, tree_left, tree_right) VALUES (1, 'root', 'root', 0, 1);";
        $sql .= "INSERT INTO role_permission (role_id, permission_id) VALUES (1, 1)";
        $this->getEntityManager()
            ->getConnection()
            ->executeQuery($sql);
    }

    /**
     */
    public function add(NodeInterface $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(NodeInterface $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getPathId(string $path,): int
    {
        return $this->pathId($path, RbacRoleNotFoundException::class);
    }

    public function getById(int $nodeId): Role
    {
        $node = $this->find($nodeId);
        if (empty($node)) {
            throw new RbacRoleNotFoundException();
        }

        return $node;
    }

    public function getPath(int $nodeId): array
    {
        return $this->getPathFunc($nodeId, RbacRoleNotFoundException::class);
    }

    public function getChildren(int $nodeId): array
    {
        $sql = "
            SELECT
                node.*,
                (COUNT(parent.id)-1 - (sub_tree.innerDepth )) AS depth
            FROM
                {$this->tableName} as node,
                {$this->tableName} as parent,
                {$this->tableName} as sub_parent,
                (
                    SELECT
                        node.id,
                        (COUNT(parent.id) - 1) AS innerDepth
                    FROM
                        {$this->tableName} AS node,
                        {$this->tableName} AS parent
                    WHERE
                        node.tree_left BETWEEN parent.tree_left AND parent.tree_right
                        AND (node.id = :nodeI)
                    GROUP BY
                        node.id
                    ORDER BY
                        node.tree_left
                ) AS sub_tree
            WHERE
                node.tree_left BETWEEN parent.tree_left AND parent.tree_right
                AND node.tree_left BETWEEN sub_parent.tree_left AND sub_parent.tree_right
                AND sub_parent.id = sub_tree.id
            GROUP BY
                node.id
            HAVING
                depth = 1
            ORDER BY
                node.tree_left
        ";

        $rsm = new ResultSetMapping();
        $rsm->addEntityResult($this->getClassName(), 'node');
        $query = $this->getEntityManager()
            ->createNativeQuery($sql, $rsm);
        $query->setParameter(':nodeId', $nodeId);

        $result = $query->getResult();

        if (empty($result)) {
            throw new RbacRoleNotFoundException();
        }

        return $result;
    }

    public function deletePermissions(RoleInterface $role): RoleInterface
    {
        $role->setPermissions(null);
        $this->add($role, true);

        return $role;
    }

    public function hasPermission(int $roleId, int $permissionId): bool
    {
        $pdo = $this->getEntityManager()
            ->getConnection();

        $sql = "
            SELECT
                COUNT(*) AS result
                FROM role_permission
                INNER JOIN {$this->permissionTableName} AS permission ON permission.id = role_permission.permission_id
                INNER JOIN {$this->tableName} AS role ON role.id = role_permission.role_id
            WHERE
                role.tree_left BETWEEN
                    (SELECT tree_left FROM {$this->tableName} WHERE ID = :roleId)
                    AND
                    (SELECT tree_right FROM {$this->tableName} WHERE ID = :roleId)
                AND
                    permission.id IN (
                        SELECT
                            parent.id
                        FROM
                            {$this->permissionTableName} AS node,
                            {$this->permissionTableName} AS parent
                        WHERE
                            node.tree_left BETWEEN parent.tree_left AND parent.tree_right
                            AND node.ID= :permissionId
                        ORDER BY parent.tree_left
                    )
        ";
        $query = $pdo->prepare($sql);
        $query->bindValue(":roleId", $roleId);
        $query->bindValue(":permissionId", $permissionId);
        $stmt = $query->executeQuery();

        if ($stmt->rowCount() == 0) {
            return false;
        }

        $row = $stmt->fetchAssociative();
        return $row['result'] >= 1;
    }

    public function hasRole(int $roleId, mixed $userId): bool
    {
        $pdo = $this->getEntityManager()
            ->getConnection();

        $sql = "
            SELECT
                COUNT(*) as result
            FROM
                user_role
            INNER JOIN
                {$this->tableName} AS TRdirect ON (TRdirect.id=user_role.role_id)
            INNER JOIN
                {$this->tableName} AS TR ON (TR.tree_left BETWEEN TRdirect.tree_left AND TRdirect.tree_right)
            WHERE
                user_role.user_id = :userId AND TR.ID = :roleId
        ";
        $query = $pdo->prepare($sql);
        $query->bindValue(":roleId", $roleId);
        $query->bindValue(":userId", $userId);
        $stmt = $query->executeQuery();
        if ($stmt->rowCount() == 0) {
            return false;
        }

        $row = $stmt->fetchAssociative();
        return $row['result'] >= 1;
    }

    public function addNode(string $code, string $description, int $parentId = NodeManagerInterface::ROOT_ID): RoleInterface
    {
        /** @var RoleInterface $node */
        $node = $this->updateForAdd($parentId, $this->getClassName(), $code, $description);

        return $node;
    }
}
