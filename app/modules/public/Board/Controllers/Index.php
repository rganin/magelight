<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 01.12.12
 * Time: 18:50
 * To change this template use File | Settings | File Templates.
 */

namespace Board\Controllers;



class Index extends \Magelight\Controller
{
    /**
     * Before execute handler
     */
    public function beforeExecute()
    {
        $this->_view = \Magelight\Core\Blocks\Document::forge();
        $this->_view->sectionAppend('body', \Board\Blocks\Body::forge());
        $this->_view->sectionAppend('content', \Board\Blocks\Home::forge());
    }

    public function indexAction()
    {


        $this->renderView();
    }

    public function loginAction()
    {

    }

    public function serviceloginAction()
    {
        $s = file_get_contents('http://ulogin.ru/token.php?token='
            . $this->request()->getPost('token')
            . '&host='
            . $this->server()->getCurrentDomain());
        $userData = json_decode($s, true);

        $user = \Board\Models\User::orm()
            ->whereEq('openid_uid', $userData['uid'])
            ->whereEq('openid_provider', $userData['network'])
            ->fetchModel();

        if (empty($user)) {
            $user = \Board\Models\User::forge([
                'is_registered'   => 1,
                'date_register'   => time(),
                'openid_provider' => $userData['network'],
                'openid_identity' => $userData['identity'],
                'openid_uid'      => $userData['uid'],
                'name'            => $userData['first_name'] . ' ' . $userData['last_name']
            ], true);
            $user->save(true);
        }
        var_dump($user->id);
        $this->session()->set('user_id', $user->id);
//        $this->redirect($this->url('/'));
    }
}