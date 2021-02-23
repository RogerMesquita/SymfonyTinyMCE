<?php

namespace App\Services;

use App\Entity\Attachment;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AttachmentManager
{

    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;

    public function __construct(ContainerInterface $container,EntityManagerInterface $manager)
    {
        $this->container = $container;
        $this->manager = $manager;
    }

    public function uploadAttachment(UploadedFile $file,Post $post){
        $filename = md5(uniqid()).'.'.$file->guessExtension();

        $file->move(
            $this->getUploadsDir(),
            $filename
        );
        $attachment = new Attachment();
        $attachment->setFilename($filename);
        $attachment->setPath('/uploads/'.$filename);
        $attachment->setPost($post);
        $post->addAttachment($attachment);

        $this->manager->persist($attachment);
        $this->manager->flush();

        return [
            'filename' => $filename,
            'path' => '/uploads/'.$filename
        ];

    }

    public function removeAttachment(?string $filename){
        if(!empty($filename)){
            $filesystem = new Filesystem();
            $filesystem->remove($this->getUploadsDir().$filename);
        }
    }

    public function getUploadsDir(){
        return $this->container->getParameter('uploads');
    }

}