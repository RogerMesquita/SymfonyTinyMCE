<?php

namespace App\Controller;

use App\Entity\Post;
use App\Services\AttachmentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AttachmentController extends AbstractController
{

    /**
     * @var AttachmentManager
     */
    private AttachmentManager $attachmentManager;

    public function __construct(AttachmentManager $attachmentManager )
    {
        $this->attachmentManager = $attachmentManager;
    }

    /**
     * @param Request $request
     * @param Post $post
     * @return Response
     */
    #[Route('/attachment/{id}', name: 'attachment')]
    public function index(Request $request,Post $post): Response
    {

        $file = $request->files->get('file');

        $filenameAndPath = $this->attachmentManager->uploadAttachment($file,$post);

       return $this->json([
           'location' => $filenameAndPath['path']
       ]);
    }
}
