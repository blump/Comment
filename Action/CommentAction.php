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

use Comment\EventListeners\CommentCreateEvent;
use Comment\EventListeners\CommentDefinitionEvent;
use Comment\EventListeners\CommentEvent;
use Comment\EventListeners\CommentEvents;
use Comment\EventListeners\CommentUpdateEvent;
use Comment\Exception\InvalidDefinitionException;
use Comment\Model\Comment;
use Comment\Comment as CommentModule;
use Comment\Model\CommentQuery;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Thelia\Exception\NotImplementedException;
use Thelia\Model\ConfigQuery;
use Thelia\Model\MetaData;
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

    public function create(CommentCreateEvent $event)
    {
        $comment = new Comment();

        $comment
            ->setRef($event->getRef())
            ->setRefId($event->getRefId())
            ->setCustomerId($event->getCustomerId())
            ->setUsername($event->getUsername())
            ->setEmail($event->getEmail())
            ->setTitle($event->getTitle())
            ->setContent($event->getContent())
            ->setStatus($event->getStatus())
            ->setVerified($event->isVerified())
            ->setRating($event->getRating())
            ->save()
        ;
        
        $event->setComment($comment);

        if (Comment::ACCEPTED === $comment->getStatus()) {
            $this->dispatchRatingCompute(
                $event->getDispatcher(),
                $comment->getRef(),
                $comment->getRefId()
            );
        }

    }

    public function update(CommentUpdateEvent $event)
    {
        if (null == $comment = CommentQuery::create()->findPk($event->getId())) {
            $comment
                ->setRef($event->getRef())
                ->setRefId($event->getRefId())
                ->setCustomerId($event->getCustomerId())
                ->setUsername($event->getUsername())
                ->setEmail($event->getEmail())
                ->setTitle($event->getTitle())
                ->setContent($event->getContent())
                ->setStatus($event->getStatus())
                ->setVerified($event->isVerified())
                ->setRating($event->getRating())
                ->save()
            ;
            $event->setComment($comment);

            $this->dispatchRatingCompute(
                $event->getDispatcher(),
                $comment->getRef(),
                $comment->getRefId()
            );

        }
    }

    public function delete(CommentEvent $event)
    {
        if (null == $comment = CommentQuery::create()->findPk($event->getId())) {
            $comment->delete();

            $event->setComment($comment);

            if (Comment::ACCEPTED === $comment->getStatus()) {
                $this->dispatchRatingCompute(
                    $event->getDispatcher(),
                    $comment->getRef(),
                    $comment->getRefId()
                );
            }
        }
    }

    public function abuse(CommentEvent $event)
    {
        if (null == $comment = CommentQuery::create()->findPk($event->getId())) {
            $comment->setAbuse($comment->getAbuse() + 1);
            $comment->save();

            $event->setComment($comment);
        }
    }

    public function statusChange(CommentEvent $event)
    {
        $changed = false;

        if (null !== $comment = CommentQuery::create()->findPk($event->getId())) {
            if ($comment->getStatus() !== $event->getNewStatus()) {
                $comment->setStatus($event->getNewStatus());
                $comment->save();

                $event->setComment($comment);

                $this->dispatchRatingCompute(
                    $event->getDispatcher(),
                    $comment->getRef(),
                    $comment->getRefId()
                );
            }
        }
    }

    public function productRatingCompute(CommentEvent $event)
    {
        if ('product' === $event->getRef()) {

            $product = ProductQuery::create()->findPk($event->getRefId());
            if (null !== $product) {

                $query = CommentQuery::create()
                    ->filterByRef('product')
                    ->filterByRefId($product->getId())
                    ->filterByStatus(Comment::ACCEPTED)
                    ->withColumn("AVG(RATING)", 'AVG_RATING')
                    ->select('AVG_RATING')
                ;

                $rating = $query->findOne();

                if (null !== $rating) {
                    $rating = round($rating, 2);

                    $event->setRating($rating);

                    MetaDataQuery::setVal(
                        Comment::META_KEY_RATING,
                        MetaData::PRODUCT_KEY,
                        $product->getId(),
                        $rating
                    );
                }

            }

        }

    }

    /**
     * Dispatch an event to compute an average rating
     *
     * @param string $ref
     * @param int $refId
     */
    protected function dispatchRatingCompute($dispatcher, $ref, $refId)
    {
        $ratingEvent = new CommentEvent(['ref', 'ref_id', 'rating']);

        $ratingEvent
            ->setRef($ref)
            ->setRefId($refId)
        ;

        $dispatcher->dispatch(
            CommentEvents::COMMENT_RATING_COMPUTE,
            $ratingEvent
        );
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

        if (null !== $event->getCustomer()) {
            // is customer already have published something
            $comment = CommentQuery::create()
                ->filterByCustomerId($event->getCustomer()->getId())
                ->filterByRef($event->getRef())
                ->filterByRefId($event->getRefId())
                ->findOne();

            if (null !== $comment) {
                $event->setComment($comment);
            }
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

        $verified = false;
        if (null !== $event->getCustomer()) {
            // customer has bought the product
            $productBoughtCount = OrderProductQuery::getSaleStats(
                $product->getRef(),
                null,
                null,
                [2, 3, 4],
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
        } else {
            $verified = false;
        }

        $event->setVerified($verified);
        $event->setRating(true);
    }

    public function getContentDefinition(CommentDefinitionEvent $event)
    {
        $event->setVerified(true);
        $event->setRating(false);
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
            CommentEvents::COMMENT_STATUS_UPDATE => ['statusChange', 128],
            CommentEvents::COMMENT_RATING_COMPUTE => ['productRatingCompute', 128],
            CommentEvents::COMMENT_GET_DEFINITION => ['getDefinition', 128],
            CommentEvents::COMMENT_GET_DEFINITION_PRODUCT => ['getProductDefinition', 128],
            CommentEvents::COMMENT_GET_DEFINITION_CONTENT => ['getContentDefinition', 128],
        ];
    }
}