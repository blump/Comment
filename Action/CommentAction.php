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

use Comment\EventListeners\CommentDefinitionEvent;
use Comment\EventListeners\CommentEvent;
use Comment\EventListeners\CommentEvents;
use Comment\Exception\InvalidDefinitionException;
use Comment\Model\Comment;
use Comment\Comment as CommentModule;
use Comment\Model\CommentQuery;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Thelia\Exception\NotImplementedException;
use Thelia\Model\ConfigQuery;
use Thelia\Model\MetaDataQuery;
use Thelia\Model\OrderProductQuery;
use Thelia\Model\ProductQuery;

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
    /** @var null|TranslatorInterface */
    protected $translator = null;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function create(CommentEvent $event)
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
        
        $event->setComment($comment);
    }

    public function update(CommentEvent $event)
    {
        throw new NotImplementedException('Not yet implemented');
    }

    public function delete(CommentEvent $event)
    {
        throw new NotImplementedException('Not yet implemented');
    }

    public function abuse(CommentEvent $event)
    {
        throw new NotImplementedException('Not yet implemented');
    }

    public function statusChange(CommentEvent $event)
    {
        throw new NotImplementedException('Not yet implemented');
    }

    public function getDefinition(CommentDefinitionEvent $event)
    {
        $config = $event->getConfig();

        if (!in_array($event->getRef(), $config['ref_allowed'])) {
            throw new InvalidDefinitionException(
                $this->translator->trans(
                    "Reference %ref is not allowed",
                    ['%ref' => $event->getRef()],
                    CommentModule::getModuleCode()
                )
            );
        }

        // is only customer is authorized to publish
        if ($config['only_customer'] && null === $event->getCustomer()) {
            throw new InvalidDefinitionException(
                $this->translator->trans(
                    "Only customer are allowed to publish comment",
                    [],
                    CommentModule::getModuleCode()
                ),
                false
            );
        }

        $eventName = CommentEvents::COMMENT_GET_DEFINITION . "." . $event->getRef();
        $event->getDispatcher()->dispatch($eventName, $event);

        // is customer already have published something
        $comment = CommentQuery::create()
            ->filterByCustomerId($event->getCustomer()->getId())
            ->filterByRef($event->getRef())
            ->filterByRefId($event->getRefId())
            ->findOne()
        ;

        if (null !== $comment) {
            $event->setComment($comment);
        }
    }


    public function getProductDefinition(CommentDefinitionEvent $event)
    {
        $config = $event->getConfig();

        $product = ProductQuery::create()->findPk($event->getRefId());
        if (null === $product) {
            throw new InvalidDefinitionException(
                $this->translator->trans(
                    "Product %id does not exist",
                    ['%ref' => $event->getRef()],
                    CommentModule::getModuleCode()
                )
            );
        }

        // is comment is authorized on this product
        $commentProductActivated = MetaDataQuery::getVal(
            'comment_activated',
            \Thelia\Model\MetaData::PRODUCT_KEY,
            $product->getId()
        );

        // not defined, get the global config
        if (null === $commentProductActivated) {
            if (!$config['activated']) {
                throw new InvalidDefinitionException(
                    $this->translator->trans(
                        "Comment not activated on this element.",
                        ['%ref' => $event->getRef()],
                        CommentModule::getModuleCode()
                    )
                );
            }
        }

        // customer has bought the product
        $productBoughtCount = OrderProductQuery::getSaleStats(
            $product->getRef(),
            null,
            null,
            [2,3,4],
            $event->getCustomer()->getId()
        );

        if ($config['only_verified']) {
            if (0 === $productBoughtCount) {
                throw new InvalidDefinitionException(
                    $this->translator->trans(
                        "Only customers who have bought this product can publish comment",
                        [],
                        CommentModule::getModuleCode()
                    ),
                    false
                );
            }
        }

        $verified = 0 !== $productBoughtCount;

    }

    public function getContentDefinition(CommentDefinitionEvent $event)
    {

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
        return [
            CommentEvents::COMMENT_CREATE => ['create', 128],
            CommentEvents::COMMENT_DELETE => ['delete', 128],
            CommentEvents::COMMENT_UPDATE => ['update', 128],
            CommentEvents::COMMENT_ABUSE => ['abuse', 128],
            CommentEvents::COMMENT_STATUS_UPDATE => ['statusUpdate', 128],
            CommentEvents::COMMENT_GET_DEFINITION => ['getDefinition', 128],
            CommentEvents::COMMENT_GET_DEFINITION_PRODUCT => ['getProductDefinition', 128],
            CommentEvents::COMMENT_GET_DEFINITION_CONTENT => ['getContentDefinition', 128],
        ];
    }
}