<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia                                                                       */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : info@thelia.net                                                      */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*      along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/

namespace Comment\Controller\Front;

use Comment\Comment;
use Comment\EventListeners\CommentCreateEvent;
use Comment\EventListeners\CommentDefinitionEvent;
use Comment\EventListeners\CommentEvent;
use Comment\EventListeners\CommentEvents;
use Comment\Exception\InvalidDefinitionException;
use Comment\Form\AddCommentForm;
use Exception;
use Thelia\Controller\Front\BaseFrontController;

/**
 * Class CommentController
 * @package Comment\Controller\Admin
 * @author Michaël Espeche <michael.espeche@gmail.com>
 * @author Julien Chanséaume <jchanseaume@openstudio.fr>
 */
class CommentController extends BaseFrontController
{
    const DEFAULT_VISIBLE = 0;

    public function getAction()
    {
        // only ajax
        $this->checkXmlHttpRequest();

        $definition = null;

        try {
            $definition = $this->getDefinition(
                $this->getRequest()->get('ref', null),
                $this->getRequest()->get('ref_id', null)
            );
        } catch (InvalidDefinitionException $ex) {
            if ($ex->isSilent()) {
                // Comment not authorized on this resource
                $this->accessDenied();
            }
        }

        return $this->render(
            "ajax-comments",
            [
                'ref' => $this->getRequest()->get('ref'),
                'ref_id' => $this->getRequest()->get('ref_id'),
                'start' => $this->getRequest()->get('start', 0),
                'count' => $this->getRequest()->get('count', 10),
            ]
        );

    }

    public function createAction()
    {
        // only ajax
        $this->checkXmlHttpRequest();

        $responseData = [];
        /** @var CommentDefinitionEvent $definition */
        $definition = null;

        try {
            $params = $this->getRequest()->get('admin_add_comment');
            $definition = $this->getDefinition(
                $params['ref'],
                $params['ref_id']
            );

        } catch (InvalidDefinitionException $ex) {
            if ($ex->isSilent()) {
                // Comment not authorized on this resource
                $this->accessDenied();
            } else {
                // The customer does not have minimum requirement to post comment
                $responseData = [
                    "success" => false,
                    "messages" => [$ex->getMessage()]
                ];
                return $this->jsonResponse(json_encode($responseData));
            }
        }

        $commentForm = new AddCommentForm($this->getRequest());

        // adapt form
        if (null !== $customer = $definition->getCustomer()) {
            $commentForm->getFormBuilder()->remove('username');
            $commentForm->getFormBuilder()->remove('email');
        } else {
            $commentForm->getFormBuilder()->remove('customer_id');
        }

        if (!$definition->hasRating()) {
            $commentForm->getFormBuilder()->remove('rating');
        }

        try {
            $form = $this->validateForm($commentForm);

            $event = new CommentCreateEvent();
            $event->bindForm($form);

            $event->setVerified($definition->isVerified());
            if (!$definition->getConfig()['moderate']) {
                $event->setStatus(\Comment\Model\Comment::ACCEPTED);
            }

            $this->dispatch(CommentEvents::COMMENT_CREATE, $event);

            if (null !== $event->getComment()) {
                $responseData = [
                    "success" => true,
                    "messages" => [
                        $this->translator->trans(
                            "Thank you for submitting your comment.",
                            [],
                            Comment::getModuleCode()
                        ),
                    ]
                ];
            } else {
                $responseData = [
                    "success" => false,
                    "messages" => [
                        $this->getTranslator()->trans(
                            "Sorry, an unknown error occurred. Please try again.",
                            [],
                            Comment::getModuleCode()
                        )
                    ],
                    "errors" => []
                ];
            }
        } catch (Exception $ex) {
            $responseData = [
                "success" => false,
                "messages" => [$ex->getMessage()],
                "errors" => []
            ];
        }

        return $this->jsonResponse(json_encode($responseData));
    }

    protected function getDefinition($ref, $refId)
    {
        $eventDefinition = new CommentDefinitionEvent();
        $eventDefinition
            ->setRef($ref)
            ->setRefId($refId)
            ->setCustomer($this->getSecurityContext()->getCustomerUser())
            ->setConfig(Comment::getConfig())
        ;

        $this->dispatch(
            CommentEvents::COMMENT_GET_DEFINITION,
            $eventDefinition
        );

        return $eventDefinition;
    }

    public function deleteAction()
    {

    }
}
