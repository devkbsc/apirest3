<?php

// src/AppBundle/Repository/CommentRepository.php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CommentRepository extends EntityRepository
{
    public function findAllCommentsWithoutPost()
    {
        $sql = "
            SELECT *
            FROM comment
            WHERE post is NULL
        ";

        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare($sql);
        $statement->execute();

        return $statement->fetchAll();
    }



}