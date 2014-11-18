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


namespace Comment\Hook;

use Comment\Comment;
use Comment\EventListeners\CommentDefinitionEvent;
use Comment\EventListeners\CommentEvents;
use Thelia\Core\Event\Hook\BaseHookRenderEvent;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Model\ConfigQuery;
use Thelia\Model\Exception\InvalidArgumentException;

/**
 * Class FrontHook
 * @package Comment\Hook
 * @author Julien Chanséaume <jchanseaume@openstudio.fr>
 */
class FrontHook extends BaseHook
{
    protected $parserContext = null;

    public function __construct()
    {
    }

    public function onShowComment(BaseHookRenderEvent $event)
    {
        $ref = $event->getArgument('ref')
            ? $event->getArgument('ref')
            : $this->getView();
        $refId = $event->getArgument('ref_id')
            ? $event->getArgument('ref_id')
            : $this->getRequest()->attributes['id'];

        if (null === $ref || 0 === $refId) {
            throw new InvalidArgumentException(
                $this->trans("", [], Comment::getModuleCode())
            );
        }

        $data = $this->showComment($ref, $refId);

        if (null !== $data) {
            if ($event instanceof HookRenderEvent) {
                $event->add($data);
            } else {
                $event->add(
                    [
                        'id' => 'comment',
                        'title' => $this->trans("Comments"),
                        'content' => $data
                    ]
                );
            }
        }
    }

    protected function showComment($ref, $refId)
    {

        $eventDefinition = new CommentDefinitionEvent();
        $eventDefinition
            ->setRef($ref)
            ->setRefId($refId);

        $this->dispatcher->dispatch(
            CommentEvents::COMMENT_GET_DEFINITION,
            $eventDefinition
        );

        if (empty($eventDefinition)) {
            return null;
        }

        return $this->render(
            "comment.html",
            ['definition' => $eventDefinition]
        );
    }

    private function addMessage($event, $message)
    {
        $event->add(
            [
                'id' => 'comment',
                'title' => $this->trans("Comments"),
                'content' => $message
            ]
        );
    }


    /**
     * Add the javascript script to manage comments
     *
     * @param HookRenderEvent $event
     */
    public function jsComment(HookRenderEvent $event)
    {
        $allowedRef = explode(
            ',',
            ConfigQuery::read('comment_ref_allowed', Comment::CONFIG_REF_ALLOWED)
        );

        if (in_array($this->getView(), $allowedRef)) {
            $event->add($this->render("js.html"));
        }
    }
}
