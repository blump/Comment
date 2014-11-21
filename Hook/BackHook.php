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

use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;

/**
 * Class BackHook
 * @package Comment\Hook
 * @author Julien Chanséaume <jchanseaume@openstudio.fr>
 */
class BackHook extends BaseHook
{

    public function onModuleConfiguration(HookRenderEvent $event){
        $event->add($this->render("configuration.html"));
    }

}
