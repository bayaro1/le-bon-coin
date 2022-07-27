<?php 
namespace App\Controller;

use App\Entity\Conversation;
use DateTimeImmutable;
use App\Entity\Message;
use App\Entity\Product;
use App\Form\MessageType;
use App\Persister\MessagePersister;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MessageController extends AbstractController
{
    private EntityManagerInterface $em;

    private ConversationRepository $conversationRepository;

    private MessagePersister $messagePersister;

    public function __construct(EntityManagerInterface $em, ConversationRepository $conversationRepository, MessagePersister $messagePersister)
    {
        $this->em = $em;
        $this->conversationRepository = $conversationRepository;
        $this->messagePersister = $messagePersister;
    }

    #[Route('/reply/{product_id}', name: 'message_new')]
    #[ParamConverter('product', class: Product::class, options: ['mapping' => ['product_id' => 'id']])]
    #[IsGranted('ROLE_USER')]
    public function new(Product $product, Request $request)
    {
        if($product->getUser() === $this->getUser())
        {
            return $this->redirectToRoute('product_show', [
                'product_id' => $product->getId(),
                'category' => $product->getCategory()->getName() 
            ]);
        }

        $message = new Message;
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) 
        { 
            $message->setSender($this->getUser())
                    ->setReceiver($product->getUser())
                    ->setProduct($product)
                    ;
            $this->messagePersister->persist($message);
            $this->addFlash('success', 'Votre message a bien été envoyé !');
            
            return $this->redirectToRoute('product_show', [
                'product_id' => $product->getId(),
                'category' => $product->getCategory()->getName()
            ]);
        }
        return $this->render('message/new.html.twig', [
            'product' => $product,
            'user' => $product->getUser(),
            'form' => $form->createView()
        ]);
    }

    #[Route('/messages/{conversation_id?0}', name: 'message_index')]
    #[IsGranted('ROLE_USER')]
    #[ParamConverter('selectedConversation', options: ['mapping' => ['conversation_id' => 'id']])]
    public function index(?Conversation $selectedConversation, Request $request)
    { 
        $message = new Message;
        if($selectedConversation)
        {
            $form = $this->createForm(MessageType::class, $message);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) 
            { 
                $message->setSender($this->getUser())
                        ->setReceiver($selectedConversation->getInterlocutor())
                        ->setProduct($selectedConversation->getProduct())
                        ;
                $this->messagePersister->persist($message);
            }
        }

        $form = $this->createForm(MessageType::class, null, [
            'fieldType' => TextType::class
        ]);
        return $this->render('message/index.html.twig', [
            'current_menu' => 'message',
            'conversations' => $this->conversationRepository->findAllByUser($this->getUser()),
            'selected_conversation' => $selectedConversation,
            'form' => $form->createView()
        ]);
    }
}