<?php

namespace Application\Model;

use Zend\Hydrator\AbstractHydrator;

class CategoryHydrator extends AbstractHydrator
{
    /**
     * @param Category $object
     * @param array $data
     * @return object|array
     */
    public function hydrate(array $data, $object)
    {
        if (!$object instanceof Category) {
            return $object;
        }

        if (array_key_exists('id', $data)) {
            $object->setId($data['id']);
        };

        if (array_key_exists('notes', $data)) {
            foreach($data['notes'] as $noteId) {
                $note = new Note();
                $note->setId($noteId);

                $object->addNote($note);
            }
        };

        if (array_key_exists('name', $data)) {
            $object->setTags($data['tags']);
        };

        if (array_key_exists('icon', $data)) {
            $object->setTags($data['tags']);
        };

        return $object;
    }

    /**
     * @param Category $object
     * @return array
     */
    public function extract($object)
    {
        $notes = [];

        /** @var Note $note */
        foreach ((array)$object->getNotes() as $note) {
            $notes[] = [
                'id' => $note->getId(),
                'title' => $note->getTitle(),
                'content' => $note->getContent(),
                'url' => $note->getUrl(),
                'date_published' => $note->getDatePublished(),
            ];
        }

        return [
            'id' => $object->getId(),
            'notes' => $notes,
            'name' => $object->getName(),
            'icon' => $object->getIcon()
        ];
    }
}