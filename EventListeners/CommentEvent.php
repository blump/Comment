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

namespace Comment\EventListeners;

use Comment\Model\Comment;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\Form;
use Thelia\Core\Event\ActionEvent;

/**
 *
 * This class contains all Comment events identifiers used by Comment Core
 *
 * @author Michaël Espeche <michael.espeche@gmail.com>
 * @author Julien Chanséaume <jchanseaume@openstudio.fr>
 */
class CommentEvent extends ActionEvent
{

    const COMMENT_ADD = 'comment.action.add';

    /** @var Comment */
    protected $comment = null;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * @return Comment|null
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param Comment $comment
     */
    public function setComment(Comment $comment)
    {
        $this->comment = $comment;
        return $this;
    }


    /**
     * bind form fields to parameters
     *
     * @param Form $form
     */
    public function bindForm(Form $form)
    {
        $fields = $form->getIterator();

        /** @var \Symfony\Component\Form\Form $field */
        foreach ($fields as $field) {
            $functionName = sprintf("set%s", Container::camelize($field->getName()));
            if (method_exists($this, $functionName)) {
                $this->{$functionName}($field->getData());
            } else {
                $this->parameters[$field->getName()] = $field->getData();
            }
        }
    }

}