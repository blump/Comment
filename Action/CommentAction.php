<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
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
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/

namespace Comment\Action;

use Comment\EventListeners\CommentEvent;
use Comment\Model\Comment;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 *
 * CommentAction class where all actions are managed
 *
 * Class CommentAction
 * @package Comment\Action
 * @author Michaël Espeche <michael.espeche@gmail.com>
 */
class CommentAction implements EventSubscriberInterface
{

    public function addComment(CommentEvent $event)
    {
     
        $comment = new Comment();

        $comment->setCustomerId($event->getCustomerId())
                ->setUsername($event->getUsername())
                ->setEmail($event->getEmail())
                ->setContent($event->getContent())
                ->setRef($event->getRef())
                ->setRefId($event->getRefId())
                ->setVisible($event->getVisible())
                ->save();
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            CommentEvent::COMMENT_ADD => array('addComment', 128)            
        );
    }
}