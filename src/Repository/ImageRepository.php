<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class ImageRepository extends EntityRepository
{
    public function deleteImage($image)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($image);
        $entityManager->flush();
    }

}