<?php

namespace App\Form\DataTransformer;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class TagTransformer implements DataTransformerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TagRepository          $tagRepository,
    )
    {
    }

    public function transform($tags): string
    {
        if ($tags === null) {
            return '';
        }

        return implode(',', array_map(fn($tag) => $tag->getName(), $tags->toArray()));
    }

    public function reverseTransform(mixed $value = null): ?ArrayCollection
    {
        if (!$value) {
            return null;
        }

        $items = explode(',', $value);
        $items = array_map('trim', $items);
        $items = array_unique($items);

        $tags = new ArrayCollection();

        foreach ($items as $item) {
            $tag = $this->tagRepository->findOneBy(['name' => $item]);

            if ($tag === null) {
                $tag = new Tag();
                $tag->setName($item);
                $this->entityManager->persist($tag);
            }
            $tags->add($tag);
        }
        $this->entityManager->flush();

        return $tags;
    }
}

?>