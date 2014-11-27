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


namespace Comment\Form;

use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Form\BaseForm;

/**
 * Class CommentAbuseForm
 * @package Comment\Form
 * @author Julien Chanséaume <jchanseaume@openstudio.fr>
 */
class CommentAbuseForm extends BaseForm
{
    protected function buildForm()
    {
        $this
            ->formBuilder
            ->add(
                'id',
                'comment_id'
            )
        ;
    }



    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return 'comment_abuse';
    }
}
