<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Milestone Celebration / Inc / Class
 */

namespace PH7;

use PH7\Framework\Core\Kernel;
use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\PH7Tpl;
use PH7\Framework\Mail\Mailable;
use PH7\Framework\Mvc\Model\DbConfig;

class MilestoneNotifier
{
    const MAIL_TEMPLATE_FILE_PATH = '/tpl/mail/sys/mod/milestone-celebration/admin-notifier.tpl';

    /** @var UserCoreModel */
    private $oUserModel;

    /** @var Mailable */
    private $oMail;

    /** @var PH7Tpl */
    private $oView;

    public function __construct(UserCoreModel $oUserModel, Mailable $oMailEngine, PH7Tpl $oView)
    {
        $this->oMail = $oMailEngine;
        $this->oUserModel = $oUserModel;
        $this->oView = $oView;
    }

    /**
     * @return int
     *
     * @throws Framework\Layout\Tpl\Engine\PH7Tpl\Exception
     */
    public function sendEmailToAdmin()
    {
        $iTotalUsers = $this->oUserModel->total();

        $this->oView->greeting = t('Hi there! 😊');
        $this->oView->content = t('Something AMAZING amd AWESOME just happened to your website!!!') . '<br />';
        $this->oView->content .= t('Indeed, your website reached the %0% users!!! Congratulations! 😍', $iTotalUsers);
        $this->oView->become_patron = t('Do you think it is the right time to <a href="%0%">Become a Patron</a> and support the development of the software?', Kernel::PATREON_URL);

        $sMessageHtml = $this->oView->parseMail(
            PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_MAIL_NAME . self::MAIL_TEMPLATE_FILE_PATH,
            DbConfig::getSetting('adminEmail')
        );

        $aInfo = [
            'subject' => t('Your Website Reached %0% users!!! 🎉', $iTotalUsers)
        ];

        return $this->oMail->send(
            $aInfo,
            $sMessageHtml,
            Mailable::HTML_FORMAT
        );
    }
}
