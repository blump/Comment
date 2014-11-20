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

namespace Comment;

use Comment\Model\CommentQuery;
use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Install\Database;
use Thelia\Model\ConfigQuery;
use Thelia\Module\BaseModule;

/**
 * Class Comment
 * @package Comment
 *
 * @author Michaël Espeche <michael.espeche@gmail.com>
 * @author Julien Chanséaume <jchanseaume@openstudio.fr>
 */
class Comment extends BaseModule
{

    /**  Use comment */
    const CONFIG_ACTIVATED = 1;

    /**  Use moderation */
    const CONFIG_MODERATE = 1;

    /** Allowed ref */
    const CONFIG_REF_ALLOWED = 'product,content';

    /** Only customers are abled to post comment */
    const CONFIG_ONLY_CUSTOMER = 1;

    /** Allow only verified customer (for product, customers that have bought the product) */
    const CONFIG_ONLY_VERIFIED = 1;


    public function postActivation(ConnectionInterface $con = null)
    {
        // Config
        if (null === ConfigQuery::read('comment_activated')) {
            ConfigQuery::write('comment_activated', Comment::CONFIG_ACTIVATED);
        }

        if (null === ConfigQuery::read('comment_moderate')) {
            ConfigQuery::write('comment_moderate', Comment::CONFIG_MODERATE);
        }

        if (null === ConfigQuery::read('comment_ref_allowed')) {
            ConfigQuery::write('comment_ref_allowed', Comment::CONFIG_REF_ALLOWED);
        }

        if (null === ConfigQuery::read('comment_only_customer')) {
            ConfigQuery::write('comment_only_customer', Comment::CONFIG_ONLY_CUSTOMER);
        }

        if (null === ConfigQuery::read('comment_only_verified')) {
            ConfigQuery::write('comment_only_verified', Comment::CONFIG_ONLY_VERIFIED);
        }

        // Schema
        try {
            CommentQuery::create()->findOne();
        } catch (\Exception $ex) {
            $database = new Database($con->getWrappedConnection());
            $database->insertSql(null, [__DIR__ . DS . 'Config' . DS . 'thelia.sql']);
        }
    }

    public static function getConfig()
    {
        $config = [
            'activated' => (
                intval(ConfigQuery::read('comment_activated', self::CONFIG_ACTIVATED)) === 1
            ),
            'moderate' => (
                intval(ConfigQuery::read('comment_moderate', self::CONFIG_MODERATE)) === 1
            ),
            'ref_allowed' => explode(
                ',',
                ConfigQuery::read('comment_ref_allowed', self::CONFIG_REF_ALLOWED)
            ),
            'only_customer' => (
                intval(ConfigQuery::read('comment_only_customer', self::CONFIG_ONLY_CUSTOMER)) === 1
            ),
            'only_verified' => (
                intval(ConfigQuery::read('comment_only_verified', self::CONFIG_ONLY_VERIFIED)) === 1
            )
        ];

        return $config;
    }
}
