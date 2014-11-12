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

    protected $customerId;
    protected $username;
    protected $email;
    protected $content;
    protected $ref;
    protected $refId;
    protected $visible;
    protected $comment;


    /**
     * Constructor
     * @param type $username
     * @param type $email
     * @param type $content
     * @param type $ref
     * @param type $refId
     * @param type $visible
     */
    function __construct($username, $email, $content, $ref, $refId, $visible) {
        $this->username = $username;
        $this->email = $email;
        $this->content = $content;
        $this->ref = $ref;
        $this->refId = $refId;
        $this->visible = $visible;
    }
       
    public function getCustomerId() {
        return $this->customerId;
    }

    public function setCustomerId($customerId) {
        $this->customerId = $customerId;
        
        return $this;
    }
            
    public function getUsername() {
        return $this->username;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getContent() {
        return $this->content;
    }

    public function getRef() {
        return $this->ref;
    }

    public function getRefId() {
        return $this->refId;
    }

    public function getVisible() {
        return $this->visible;
    }

    public function setUsername($username) {
        $this->username = $username;
        
        return $this;
    }

    public function setEmail($email) {
        $this->email = $email;
        
        return $this;
    }

    public function setContent($content) {
        $this->content = $content;
        
        return $this;
    }

    public function setRef($ref) {
        $this->ref = $ref;
        
        return $this;
    }

    public function setRefId($refId) {
        $this->refId = $refId;
        
        return $this;
    }

    public function setVisible($visible) {
        $this->visible = $visible;
        
        return $this;
    }
    
    public function getComment() {
        return $this->comment;
    }

    public function setComment($comment) {
        $this->comment = $comment;
        
        return $this;
    }


}