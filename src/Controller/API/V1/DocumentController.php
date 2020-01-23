<?php

namespace App\Controller\API\V1;

use App\Entity\Attachment;
use App\Entity\Document;
use App\Entity\Thumbnail;
use App\Event\AttachmentUploadedEvent;
use App\Interfaces\UploadAwareInterface;
use App\Repository\DocumentRepository;
use App\Repository\ThumbnailRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;


/**
 * @Route("/documents")
 */
class DocumentController extends AbstractFOSRestController
{
    /**
     * @var DocumentRepository
     */
    private $documentRepository;

    /**
     * @var ThumbnailRepository
     */
    private $thumbnailRepository;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var UploadAwareInterface
     */
    private $uploader;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * DocumentController constructor.
     *
     * @param DocumentRepository       $documentRepository
     * @param ThumbnailRepository      $thumbnailRepository
     * @param ValidatorInterface       $validator
     * @param SerializerInterface      $serializer
     * @param UploadAwareInterface     $uploader
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        DocumentRepository $documentRepository,
        ThumbnailRepository $thumbnailRepository,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        UploadAwareInterface $uploader,
        EventDispatcherInterface $dispatcher
    ) {
        $this->documentRepository  = $documentRepository;
        $this->thumbnailRepository = $thumbnailRepository;
        $this->validator           = $validator;
        $this->serializer          = $serializer;
        $this->uploader            = $uploader;
        $this->dispatcher          = LegacyEventDispatcherProxy::decorate($dispatcher);
    }

    /**
     * @param Request $request
     *
     * @Rest\Get("/")
     * @SWG\Tag(name="Documents")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns all documents.",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Document::class))
     *     )
     * )
     *
     * @return Response
     */
    public function getDocuments(Request $request)
    {
        $documents = $this->documentRepository->findAll();

        $view = $this->view($documents, Response::HTTP_OK);

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @Rest\Get("/{document_id}", requirements={"document_id"="\d+"})
     * @SWG\Tag(name="Documents")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns document by id.",
     *     @Model(type=Document::class)
     * )
     *
     * @return Response
     */
    public function getDocument(Request $request)
    {
        $document = $this->documentRepository->find($request->get('document_id'));
        $view     = $this->view($document, Response::HTTP_OK);

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @Rest\Post("/")
     * @SWG\Tag(name="Documents")
     *
     * @SWG\Parameter(
     *     name="title",
     *     in="body",
     *     type="string",
     *     description="The field used to create document",
     *     @SWG\Schema(type="object",
     *         @SWG\Property(property="title", type="string")
     *     )
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Creates document.",
     *     @Model(type=Document::class)
     * )
     *
     * @return Response
     */
    public function createDocument(Request $request)
    {
        $document = $this->serializer->deserialize($request->getContent(), Document::class, "json");
        $errors   = $this->validator->validate($document);
        $view     = null;
        if ($errors->count() > 0) {
            $view = $this->view($errors, Response::HTTP_BAD_REQUEST);
        } else {
            $this->documentRepository->save($document);
            $view = $this->view($document, Response::HTTP_OK);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @Rest\Get("/{document_id}/attachment", requirements={"document_id"="\d+"})
     * @SWG\Tag(name="Attachments")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns attachment."
     * )
     *
     * @return Response
     */
    public function getAttachment(Request $request)
    {
        $document = $this->documentRepository->find($request->get('document_id'));
        if (! $document) {
            throw new ResourceNotFoundException('Document not found.');
        }

        if(! $attachment = $document->getAttachment()){
            throw new ResourceNotFoundException('Attachment not found.');
        }

        $file = new File(sprintf('%s/%s', $this->uploader->getUploadDir(), $attachment->getFilename()));

        return $this->file($file);
    }

    /**
     * @param Request $request
     *
     * @Rest\Post("/{document_id}/attachment", requirements={"document_id"="\d+"})
     * @SWG\Tag(name="Attachments")
     *
     * @SWG\Parameter(
     *     name="file",
     *     in="formData",
     *     type="file",
     *     description="The field used to add attachment"
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Creates attachment.",
     *     @Model(type=Attachment::class)
     * )
     *
     * @return Response
     */
    public function createAttachment(Request $request)
    {

        $document = $this->documentRepository->find($request->get('document_id'));
        if (! $document) {
            throw new ResourceNotFoundException('Document not found.');
        }

        $attachment = $document->getAttachment();
        if (! $attachment) {
            $attachment = new Attachment();
        }
        $file = $request->files->get('file');
        $attachment->setFile($file);
        $errors = $this->validator->validate($attachment);
        $view   = null;

        if ($errors->count() > 0) {
            $view = $this->view($errors, Response::HTTP_BAD_REQUEST);
        } else {
            $filename = $this->uploader->upload($file);
            $attachment->setFilename($filename);
            $document->setAttachment($attachment);
            $this->documentRepository->save($document);
            $view = $this->view($document, Response::HTTP_OK);
            $this->dispatcher->dispatch(
                new AttachmentUploadedEvent($attachment, $this->uploader->getUploadDir()),
                AttachmentUploadedEvent::NAME
            );
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @Rest\Delete("/{document_id}", requirements={"document_id"="\d+"})
     * @SWG\Tag(name="Documents")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Deletes document by id.",
     *
     * )
     *
     * @return Response
     */
    public function deleteDocument(Request $request)
    {
        $document = $this->documentRepository->find($request->get('document_id'));
        if (! $document) {
            throw new ResourceNotFoundException('Document not found.');
        }
        $this->documentRepository->remove($document);
        $view = $this->view('Document was deleted', Response::HTTP_OK);

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @Rest\Get("/{document_id}/attachment/thumbnails", requirements={"document_id"="\d+"})
     * @SWG\Tag(name="Thumbnails")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns thumbnails.",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Thumbnail::class))
     *     )
     * )
     *
     * @return Response
     */
    public function getThumbnails(Request $request)
    {
        $document = $this->documentRepository->find($request->get('document_id'));
        if (! $document) {
            throw new ResourceNotFoundException('Document not found.');
        }

        if (! $document->getAttachment()) {
            throw new ResourceNotFoundException('Attachment not found.');
        }
        $view = $this->view($document->getAttachment()->getThumbnails(), Response::HTTP_OK);

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @Rest\Get("/{document_id}/attachment/thumbnails/{thumb_id}", requirements={"document_id"="\d+",
     *                                                                              "thumb_id"="\d+"})
     * @SWG\Tag(name="Thumbnails")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns thumbnail by id."
     * )
     *
     * @return Response
     */
    public function getThumbnail(Request $request)
    {
        $document = $this->documentRepository->find($request->get('document_id'));
        if (! $document) {
            throw new ResourceNotFoundException('Document not found.');
        }

        $thumbId = $request->get('thumb_id');

        $thumbnail = $this->thumbnailRepository->findThumbnailById($document, $thumbId);

        if(! $thumbnail){
            throw new ResourceNotFoundException('Thumbnail not found.');
        }

        $file = new File(sprintf('%s/%s', $this->uploader->getUploadDir(), $thumbnail->getFilename()));

        return $this->file($file);
    }
}
