<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia                                                                       */
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
/*      along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/

namespace Comment\Controller\Back;

use Comment\Comment;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Model\ConfigQuery;
use Thelia\Tools\URL;

/**
 * Class CommentController
 * @package Comment\Controller\Back
 * @author Julien Chanséaume <jchanseaume@openstudio.fr>
 */
class CommentController extends BaseAdminController
{
    public function saveConfiguration()
    {

        if (null !== $response = $this->checkAuth([AdminResources::MODULE], ['comment'], AccessManager::UPDATE)
        ) {
            return $response;
        }

        $form = new \Comment\Form\ConfigurationForm($this->getRequest());
        $message = "";

        $response = null;

        try {
            $vform = $this->validateForm($form);
            $data = $vform->getData();

            ConfigQuery::write(
                'comment_activated',
                $data['activated'] ? '1' : '0'
            );
            ConfigQuery::write(
                'comment_moderate',
                $data['moderate'] ? '1' : '0'
            );
            ConfigQuery::write('comment_ref_allowed', $data['ref_allowed']);
            ConfigQuery::write(
                'comment_only_customer',
                $data['only_customer'] ? '1' : '0'
            );
            ConfigQuery::write(
                'comment_only_verified',
                $data['only_verified'] ? '1' : '0'
            );
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }
        if ($message) {
            $form->setErrorMessage($message);
            $this->getParserContext()->addForm($form);
            $this->getParserContext()->setGeneralError($message);

            return $this->render(
                "module-configure",
                ["module_code" => Comment::getModuleCode()]
            );
        }

        return RedirectResponse::create(
            URL::getInstance()->absoluteUrl("/admin/module/" . Comment::getModuleCode())
        );
    }
}
