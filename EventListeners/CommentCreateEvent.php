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


namespace Comment\EventListeners;

/**
 * Class CommentCreateEvent
 * @package Comment\EventListeners
 * @author Julien Chanséaume <jchanseaume@openstudio.fr>
 *
 * @method getRef
 * @method getRefId
 * @method getCustomerId
 * @method getUsername
 * @method getEmail
 * @method getTitle
 * @method getContent
 * @method getStatus
 * @method isVerified
 * @method getRating
 *
 */
class CommentCreateEvent extends CommentEvent
{
    protected $attributes = [
        'ref', 'refId', 'customerId', 'username', 'email', 'title', 'content',
        'status', 'verified', 'rating'
    ];
}
