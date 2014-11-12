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

use Thelia\Core\Event\Hook\HookRenderBlockEvent;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;

/**
 * Class FrontHook
 * @package Comment\Hook
 * @author Julien Chanséaume <jchanseaume@openstudio.fr>
 */
class FrontHook extends BaseHook
{
    public function onProductAdditional(HookRenderBlockEvent $event)
    {
        $product = $event->getArgument('product', null);

        $event->add(
            [
                'id' => 'comment',
                'title' => $this->trans("Comments"),
                'content' => $this->render("product-additional.html")
            ]
        );
    }

    public function onContentMainBottom(HookRenderEvent $event){

        $event->add($this->render("content-main-bottom.html"));

    }

} 