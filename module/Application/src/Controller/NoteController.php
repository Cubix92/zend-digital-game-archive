<?php

namespace Application\Controller;

use Application\Form\NoteForm;
use Application\Model\Note;
use Application\Model\NoteCommand;
use Application\Model\NoteRepository;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class NoteController extends AbstractActionController
{
    protected $noteRepository;

    protected $noteCommand;

    protected $noteForm;

    public function __construct(NoteRepository $noteRepository, NoteCommand $noteCommand, NoteForm $noteForm)
    {
        $this->noteRepository = $noteRepository;
        $this->noteCommand = $noteCommand;
        $this->noteForm = $noteForm;
    }

    public function indexAction()
    {
        return new ViewModel([
            'notes' => $this->noteRepository->findAll()
        ]);
    }

    public function addAction()
    {
        $form = $this->noteForm;

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                /** @var Note $note */
                $note = $form->getData();
                $this->noteCommand->insert($note);
                $this->getEventManager()->trigger('noteAdded');
                $this->flashMessenger()->addSuccessMessage('Note was added successfull');
                return $this->redirect()->toRoute('note');
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->notFoundAction();
        }

        try {
            $note = $this->noteRepository->findById($id);
        } catch (\UnexpectedValueException $e) {
            $this->flashMessenger()->addErrorMessage('Note with identifier not found');
            return $this->redirect()->toRoute('note', ['action' => 'index']);
        }

        $form = $this->noteForm->bind($note);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->noteCommand->update($note);
                $this->getEventManager()->trigger('noteEdited');
                $this->flashMessenger()->addSuccessMessage('Note was updated successfull');
                return $this->redirect()->toRoute('note', ['action' => 'index']);
            }
        }

        return new ViewModel([
            'note' => $note,
            'form' => $form
        ]);
    }

    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->notFoundAction();
        }

        try {
            $note = $this->noteRepository->findById($id);
        } catch (\UnexpectedValueException $e) {
            $this->flashMessenger()->addErrorMessage('Note with identifier not found');
            return $this->redirect()->toRoute('note', ['action' => 'index']);
        }

        $this->noteCommand->delete($note);
        $this->getEventManager()->trigger('noteDeleted');
        $this->flashMessenger()->addSuccessMessage('Note was deleted successfull');
        return $this->redirect()->toRoute('note', ['action' => 'index']);
    }
}