<?php

namespace App\Listeners;

use App\Entity\Attachment;
use App\Entity\Post;
use App\Repository\AttachmentRepository;
use App\Services\AttachmentManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class PostListener
{

    /**
     * @var AttachmentManager
     */
    private AttachmentManager $attachmentManager;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var AttachmentRepository
     */
    private AttachmentRepository $attachmentRepository;

    public function __construct(EntityManagerInterface $entityManager,AttachmentManager  $attachmentManager,AttachmentRepository $attachmentRepository)
    {
        $this->attachmentManager = $attachmentManager;
        $this->entityManager = $entityManager;
        $this->attachmentRepository = $attachmentRepository;
    }

    public function preUpdate(Post $post, PreUpdateEventArgs $args)
    {
       if($args->hasChangedField( field: 'content')){

           $regex = '~/uploads/[a-zA-Z0-9]+\.\w+~';
           $matches = [];
           if(preg_match_all($regex,$args->getNewValue(field: 'content'),$matches) > 0 ){
               $filesnames = array_map(function ($match){
                   return basename($match);
               },$matches[0]);

               $recordsToRemove = $this->attachmentRepository->findAttachmentsToRemove($filesnames,$post->getId());

               foreach ($recordsToRemove as $record){
                   $this->entityManager->remove($record);
                   $this->attachmentManager->removeAttachment($record->getFilename());
               }
               $this->entityManager->flush();
           }
       }
    }
}