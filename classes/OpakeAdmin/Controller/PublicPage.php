<?php

namespace OpakeAdmin\Controller;

use Opake\Helper\Config;

class PublicPage extends \Opake\Controller\AbstractController
{

	public function actionIndex()
	{
		$user = $this->logged();
		if ($user) {
			$lastVisitedPage = $user->getLastVisitedPage();

			if($this->request->get('mainpage') || !$lastVisitedPage) {
				$this->redirect($this->services->get('user')->getHomePage($user));
			} else {
				$this->redirect($lastVisitedPage);
			}
		} else {
			$view = $this->pixie->view('public');
			$view->contactEmail = Config::get('app.contact_email');
			$view->addCSSList([
				'https://fonts.googleapis.com/css?family=Lato',
				'https://fonts.googleapis.com/css?family=Open+Sans:300&amp;subset=latin,cyrillic',
				'/common/vendors/bootstrap/css/bootstrap.min.css',
				'/vendors/swiper/css/swiper.min.css',
				'/css/public.css'
			]);
			$view->addJsList([
				'/common/vendors/jquery/jquery-2.1.1.min.js' => true,
				'/common/vendors/bootstrap/js/bootstrap.min.js' => true,
				'/vendors/swiper/js/swiper.min.js' => true,
				'/js/public.js' => false
			]);
			$this->response->body = $view->render();
		}
	}

	public function actionMail() {
		$this->response->headers = [
			'Content-Type: application/json'
		];
		$data = $this->request->post('message');

		$validator = $this->pixie->validate->get($data);
		$validator->field('name')->rule('filled')->error('Please fill in this field');
		$validator->field('email')->rule('filled')->error('Please fill in this field');
		$validator->field('email')->rule('email')->error('Invalid Email');
		$validator->field('text')->rule('filled')->error('Please fill in this field');

		if ($validator->valid()) {
			$smtp = new \Opake\Extentions\Mail\SMTP();
			$smtp->setConfig(Config::get('mail.accounts.default'));
			$smtp->addReplyTo(sprintf('%s <%s>', $data['name'], $data['email']));
			$smtp->send(Config::get('app.contact_email'), 'Message from ' . $data['name'] . '(' . $data['email'] . ') on www.opake.com', $data['text']);
			$this->response->body = json_encode([
			    'success' => true
			]);
		} else {
			$this->response->body = json_encode([
			    'success' => false,
			    'errors' => $validator->errors()
			]);
		}
	}

}
