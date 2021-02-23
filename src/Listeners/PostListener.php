<?php

namespace App\Listeners;

use App\Entity\Attachment;
use App\Entity\Post;
use App\Repository\AttachmentRepository;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class PostListener
{
//    /**
//     * @var AttachmentRepository
//     */
//    private AttachmentRepository $repository;
//
//    public function __construct(AttachmentRepository  $repository)
//    {
//
//        $this->repository = $repository;
//    }
    public function preUpdate(Post $post, PreUpdateEventArgs $args)
    {
       if($args->hasChangedField( field: 'content')){
           $em = $args->getEntityManager();
           /**@var AttachmentRepository $repository*/
           $repository = $em->getRepository(Attachment::class);
           $regex = '~/uploads/[a-zA-Z0-9]+\.\w+~';
           $matches = [];
           preg_match_all($regex,$args->getNewValue(field: 'content'),$matches);

           $filesnames = array_map(function ($match){
                return basename($match);
           },$matches[0]);

           $recordsToRemove = $repository->findAttachmentsToRemove($filesnames,$post->getId());

           dump($recordsToRemove);
           die();
       }
    }
}