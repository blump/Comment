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

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

class AddCommentForm extends BaseForm
{
    protected function buildForm()
    {
        $this->formBuilder
            ->add('username', 'text', [
                'constraints' => [
                    new NotBlank()
                ],
                'label' => Translator::getInstance()->trans('Username'),
                'label_attr' => [
                    'for' => 'comment_username'
                ]
            ])
            ->add('email', 'email', [
                'constraints' => [
                    new NotBlank(),
                    new Email()
                ],
                'label' => Translator::getInstance()->trans('Email'),
                'label_attr' => [
                    'for' => 'comment_mail'
                ]
            ])
            ->add('title', 'text', [
                'constraints' => [
                    new NotBlank()
                ],
                'label' => Translator::getInstance()->trans('Title'),
                'label_attr' => [
                    'for' => 'title'
                ]
            ])
            ->add('content', 'text', [
                'constraints' => [
                    new NotBlank()
                ],
                'label' => Translator::getInstance()->trans('Content'),
                'label_attr' => [
                    'for' => 'content'
                ]
            ])
            ->add('ref', 'text', [
                'constraints' => [
                    new NotBlank()
                ],
                'label' => Translator::getInstance()->trans('Ref'),
                'label_attr' => [
                    'for' => 'ref'
                ]
            ])
            ->add('ref_id', 'text', [
                'constraints' => [
                    new GreaterThan(['value' => 0])
                ],
                'label' => Translator::getInstance()->trans('Ref Id'),
                'label_attr' => [
                    'for' => 'ref_id'
                ]
            ])
            ->add('rating', 'text', [
                'constraints' => [
                    new GreaterThanOrEqual(['value' => 0]),
                    new LessThanOrEqual(['value' => 5])
                ],
                'label' => Translator::getInstance()->trans('Rating'),
                'label_attr' => [
                    'for' => 'rating'
                ]
            ])
        ;


    }
    /*
    protected function getDefinition() {
        $this->form->get('success_url');
    }
*/
    public function getName()
    {
        return 'admin_add_comment';
    }
}
