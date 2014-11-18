<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/


namespace Comment\Loop;

use Comment\Comment;
use Comment\Model\CommentQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Type;
use Thelia\Type\BooleanOrBothType;


/**
 * Class CommentLoop
 * @package Comment\Loop
 * @author Julien Chanséaume <jchanseaume@openstudio.fr>
 */
class CommentLoop extends BaseLoop implements PropelSearchLoopInterface
{
    protected $timestampable = true;

    /**
     * Definition of loop arguments
     *
     * @return \Thelia\Core\Template\Loop\Argument\ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('id'),
            Argument::createIntListTypeArgument('customer'),
            Argument::createAnyTypeArgument('ref'),
            Argument::createIntListTypeArgument('ref_id'),
            Argument::createIntListTypeArgument('status'),
            Argument::createBooleanOrBothTypeArgument('verified', 1),
            Argument::createAnyTypeArgument('locale'),
            new Argument(
                'order',
                new Type\TypeCollection(
                    new Type\EnumListType(
                        [
                            'id', 'id_reverse',
                            'status', 'status_reverse',
                            'verified', 'verified_reverse',
                            'abuse', 'abuse_reverse'
                        ]
                    )
                ),
                'manual'
            )
        );
    }

    /**
     * this method returns a Propel ModelCriteria
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        $search = CommentQuery::create();

        $id = $this->getId();
        if (null !== $id) {
            $search->filterById($id, Criteria::IN);
        }

        $customer = $this->getCustomer();
        if (null !== $customer) {
            $search->filterByCustomerId($customer, Criteria::IN);
        }

        $ref = $this->getRef();
        $refId = $this->getRefId();
        if (null !== $ref || null !== $refId) {
            if (null === $ref || null === $refId) {
                throw new \InvalidArgumentException(
                    $this->translator->trans(
                        "If 'ref' argument is specified, 'ref_id' argument should be specified",
                        [],
                        Comment::getModuleCode()
                    )
                );
            }

            $search->findByRef($ref);
            $search->findByRefId($refId, Criteria::IN);
        }

        $status = $this->getStatus();
        if ($status !== null) {
            $search->filterByStatus($status);
        }

        $verified = $this->getVerified();
        if ($verified !== BooleanOrBothType::ANY) {
            $search->filterByVerified($verified ? 1 : 0);
        }

        $locale = $this->getLocale();
        if (null !== $locale) {
            $search->filterByLocale($locale);
        }

        $orders  = $this->getOrder();
        foreach ($orders as $order) {
            switch ($order) {
                case "id":
                    $search->orderById(Criteria::ASC);
                    break;
                case "id_reverse":
                    $search->orderById(Criteria::DESC);
                    break;
                case "visible":
                    $search->orderByStatus(Criteria::ASC);
                    break;
                case "visible_reverse":
                    $search->orderByStatus(Criteria::DESC);
                    break;
                case "verified":
                    $search->orderByVerified(Criteria::ASC);
                    break;
                case "verified_reverse":
                    $search->orderByVerified(Criteria::DESC);
                    break;
                case "abuse":
                    $search->orderByAbuse(Criteria::ASC);
                    break;
                case "abuse_reverse":
                    $search->orderByAbuse(Criteria::DESC);
                    break;
                case "rating":
                    $search->orderByRating(Criteria::ASC);
                    break;
                case "rating_reverse":
                    $search->orderByRating(Criteria::DESC);
                    break;
            }
        }

        return $search;
    }

    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        /** @var \Comment\Model\Comment $comment */
        foreach ($loopResult->getResultDataCollection() as $comment) {
            $loopResultRow = new LoopResultRow($comment);

            $loopResultRow
                ->set('ID', $comment->getId())
                ->set('username', $comment->getUsername())
                ->set('email', $comment->getEmail())
                ->set('customer_id', $comment->getCustomerId())
                ->set('ref', $comment->getRef())
                ->set('ref_id', $comment->getRefId())
                ->set('title', $comment->getTitle())
                ->set('content', $comment->getContent())
                ->set('rating', $comment->getRating())
                ->set('status', $comment->getStatus())
                ->set('verified', $comment->getVerified())
                ->set('abuse', $comment->getAbuse())
            ;

            $this->addOutputFields($loopResultRow, $comment);

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }


}