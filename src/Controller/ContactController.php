<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\Type\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactController extends AbstractController
{
    public function __construct(private readonly MailerInterface $mailer, private readonly EntityManagerInterface $em,
                                private readonly SluggerInterface $slugger, private readonly TranslatorInterface $translator)
    {
    }

    #[Route('/', name: 'app_contact_index')]
    public function index(Request $request,
                          #[Autowire('%kernel.project_dir%/public/uploads/contact_attached_files')] string $attachedFilesDirectory): Response
    {
        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $attachedFile */
            $attachedFile = $form->get('attachedFile')->getData();

            if ($attachedFile) {
                $originalFilename = pathinfo($attachedFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $this->slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $attachedFile->guessExtension();

                try {
                    $attachedFile->move($attachedFilesDirectory, $newFilename);
                } catch (FileException $exception) {
                }

                $contact->setAttachedFile($newFilename);
            }

            $this->em->persist($contact);
            $this->em->flush();

            $this->sendEmail($contact, $attachedFilesDirectory);

            $this->addFlash('success', $this->translator->trans('informations_successfully_sent'));

            return $this->redirectToRoute('app_contact_index');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form,
        ]);
    }

    private function sendEmail(Contact $contact, string $attachedFilesDirectory): void
    {
        $email = (new TemplatedEmail())
            ->to('contact@cality.fr')
            ->subject($this->translator->trans('new_contact'))
            ->htmlTemplate('emails/contact_new.html.twig')
            ->context([
                'contact' => $contact
            ]);

        if (!empty($contact->getAttachedFile())) {
            $email->addPart(new DataPart(new File($attachedFilesDirectory . '/' . $contact->getAttachedFile())));
        }

        $this->mailer->send($email);
    }
}