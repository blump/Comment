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
use Thelia\Exception\MemberAccessException;

/**
 *
 * This class contains all Comment events identifiers used by Comment Core
 *
 * @author Michaël Espeche <michael.espeche@gmail.com>
 * @author Julien Chanséaume <jchanseaume@openstudio.fr>
 *
 */
class CommentEvent extends ActionEvent
{
    /** @var array attributes */
    protected $attributes = [];

    /** @var array attributes */
    protected $additionals = [];

    /** @var int */
    protected $id = null;

    /** @var Comment */
    protected $comment = null;

    /**
     * Constructor
     */
    public function __construct($attributes = [])
    {
        $this->attributes = array_merge($attributes, $this->additionals);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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

    public function __call($methodName, $args)
    {
        if (preg_match('~^(set|get|is|has)([A-Z].*)$~', $methodName, $matches)) {
            $property = Container::underscore($matches[2]);

            if (in_array($property, $this->attributes)) {
                switch ($matches[1]) {
                    case 'set':
                        if (count($args) !== 1) {
                            throw new MemberAccessException('Method ' . $methodName . ' need 1 argument');
                        }
                        $this->parameters[$property] = $args[0];
                        return $this;
                    case 'get':
                    case 'is':
                    case 'has':
                        if (count($args) !== 0) {
                            throw new MemberAccessException('Method ' . $methodName . ' does not have argument');
                        }
                        return $this->parameters[$property];
                }
            } else {
                throw new MemberAccessException('Method ' . $methodName . ' not exists');
            }
        }
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
