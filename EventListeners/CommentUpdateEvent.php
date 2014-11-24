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
 * Class CommentUpdateEvent
 * @package Comment\EventListeners
 * @author Julien Chanséaume <jchanseaume@openstudio.fr>
 */
class CommentUpdateEvent extends CommentCreateEvent
{
    protected $additionals = ['id'];
}
