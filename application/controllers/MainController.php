<?php

namespace application\controllers;

use application\core\Controller;
use application\lib\Pagination;
use application\models\Admin;

class MainController extends Controller {

	public function indexAction() {

		$pagination = new Pagination($this->route, $this->model->postsCount(), 10); //класс пагинатор. 1-й парам - номер страницы (через слеш на главной), второй - количество постов (100 = 10 страниц, Берем колличество из БД)

		$vars = [
			'pagination' => $pagination->get(),
			'list' => $this->model->postsList($this->route),
		];
		$this->view->render('Главная страница', $vars);
	}

	public function aboutAction() {
		$this->view->render('Обо мне');
	}

	public function contactAction() {
		if (!empty($_REQUEST)) {
			if (!$this->model->contactValidate($_REQUEST)) {
				$this->view->message('Error!', $this->model->error);
			} else {
				mail('larshin_oleg@mail.ru', 'Сообщение из блога', "От ".$_REQUEST['name']."\nПочта:".$_REQUEST['email']."\n ".$_REQUEST['text']);
				$this->view->message('success', 'Сообщение отправлено!');
			}
		}
		$this->view->render('Контакты');
	}

	public function postAction() {
		$adminModel = new Admin;
		$id = $this->route['id'];
		$res = $adminModel->isPostExists($id);

		if (!$res) { 
			$this->view->errorCode(404); //Если нет поста с таким id, выводим 404
		}
		$vars = [
			'data' => $adminModel->postData($id)[0],
		];
		$this->view->render('Пост', $vars);
	}

}