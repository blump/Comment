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
namespace Comment\Form;

//use Symfony\Component\Validator\Constraints\Callback;

//use Symfony\Component\Validator\ExecutionContextInterface;


use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
//use Thelia\Model\CustomerQuery;

class AddCommentForm extends BaseForm
{
    protected function buildForm()
    {
        $this->formBuilder
            ->add('username', 'text', array(
                'constraints' => array(
                    new NotBlank()                    
                ),
                'label' => Translator::getInstance()->trans('Username'),
                'label_attr' => array(
                    'for' => 'comment_username'
                )
            ))
            ->add('email', 'email', array(
                'constraints' => array(
                    new NotBlank(),
                    new Email()
                ),
                'label' => Translator::getInstance()->trans('Email'),
                'label_attr' => array(
                    'for' => 'comment_mail'
                )
            ))
            ->add('content', 'text', array(
                'constraints' => array(
                    new NotBlank()
                ),
                'label' => Translator::getInstance()->trans('Content'),
                'label_attr' => array(
                    'for' => 'comment_content'
                )
            ))
            ->add('ref', 'text', array(
                'constraints' => array(
                    new NotBlank()
                )
            ))
            ->add('ref_id', 'text', array(
                'constraints' => array(
                    new GreaterThan(array('value' => 0))
                )
            ))
        ;
    }

    public function getName()
    {
        return 'admin_add_comment';
    }
}
