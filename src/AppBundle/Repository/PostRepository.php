<?php

// src/AppBundle/Repository/PostRepository.php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PostRepository extends EntityRepository
{

    public function findAllCommentsByPost($postId)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        $sql = '
            SELECT c.*
            FROM comment c
            JOIN post p ON c.post = p.id
            WHERE p.id = :postId
        ';

        $statement = $connection->prepare($sql);
        $statement->bindValue('postId', $postId);
        $statement->execute();

        return $statement->fetchAll();
    }


    public function findUserByPost($postId)
    {
        $sql = "
            SELECT u.*
            FROM user u
            JOIN post p ON u.id = p.userId
            WHERE p.id = :postId
        ";

        $params = [
            'postId' => $postId,
        ];

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
    
}