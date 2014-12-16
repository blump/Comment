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

use Comment\Comment;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Form\BaseForm;

/**
 * Class ConfigurationForm
 * @package Comment\Form
 * @author Julien Chanséaume <jchanseaume@openstudio.fr>
 */
class ConfigurationForm extends BaseForm
{
    protected function trans($id, array $parameters = [])
    {
        return $this->translator->trans($id, $parameters, Comment::getModuleCode());
    }

    protected function buildForm()
    {
        $form = $this->formBuilder;

        $config = Comment::getConfig();

        $form
            ->add(
                "activated",
                "checkbox",
                [
                    'data' => $config['activated'],
                    'label' => $this->trans("Activated"),
                    'label_attr' => [
                        'for' => "activated",
                        'help' => $this->trans(
                            "Global activation of comments. You can control activation by product, content."
                        )
                    ],
                ]
            )
            ->add(
                "moderate",
                "checkbox",
                [
                    'data' => $config['moderate'],
                    'label' => $this->trans("Moderate"),
                    'label_attr' => [
                        'for' => "moderate",
                        'help' => $this->trans("Comments are not validated automatically.")
                    ],
                ]
            )
            ->add(
                "ref_allowed",
                "text",
                [
                    'constraints' => [
                        new NotBlank()
                    ],
                    'data' => implode(',', $config['ref_allowed']),
                    'label' => $this->trans("Allowed references"),
                    'label_attr' => [
                        'for' => "back_office_path",
                        'help' => $this->trans("which elements could use comments")
                    ],
                ]
            )
            ->add(
                "only_customer",
                "checkbox",
                [
                    'data' => $config['only_customer'],
                    'label' => $this->trans("Only customer"),
                    'label_attr' => [
                        'for' => "only_customer",
                        'help' => $this->trans("Only registered customers can post comments.")
                    ],
                ]
            )
            ->add(
                "only_verified",
                "checkbox",
                [
                    'data' => $config['only_verified'],
                    'label' => $this->trans("Only verified"),
                    'label_attr' => [
                        'for' => "only_verified",
                        'help' => $this->trans(
                            "For product comments. Only customers that bought the product can post comments."
                        )
                    ],
                ]
            );
    }

    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return "comment-configuration-form";
    }
}
