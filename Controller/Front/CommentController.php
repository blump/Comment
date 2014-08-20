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

use Comment\EventListeners\CommentEvent;
use Comment\Form\AddCommentForm;
use Exception;
use Thelia\Controller\Front\BaseFrontController;
use Thelia\Log\Tlog;

/**
 * Class CommentController
 * @package Comment\Controller\Admin
 * @author Michaël Espeche <michael.espeche@gmail.com>
 */
class CommentController extends BaseFrontController
{
    const DEFAULT_VISIBLE = 0;
    const DEFAULT_CUSTOMER_ID = null;
    
    public function createAction() {                
                
        $error_message = false;
        $commentForm = new AddCommentForm($this->getRequest());

        try {

            $form = $this->validateForm($commentForm);

            $event = new CommentEvent(
                self::DEFAULT_CUSTOMER_ID,
                $form->get('username')->getData(),
                $form->get('email')->getData(),
                $form->get('content')->getData(),
                $form->get('ref')->getData(),
                $form->get('ref_id')->getData(),
                self::DEFAULT_VISIBLE
            );

            if (null !== $customer = $this->getSecurityContext()->getCustomerUser()) {                
                $event->setCustomerId($customer->getId());
                $event->setUsername($customer->getUsername());
                $event->setEmail($customer->getEmail());
            }

            $this->dispatch(CommentEvent::COMMENT_ADD, $event);

        } catch (Exception $e) {
            $error_message = $e->getMessage();
        }
        
        Tlog::getInstance()->error(sprintf('Error during send comment : %s', $error_message));
        
        $commentForm->setErrorMessage($error_message);

        $this->getParserContext()
            ->addForm($commentForm)
            ->setGeneralError($error_message)
        ;        
        
    }

}