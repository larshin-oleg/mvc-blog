<?php

namespace application\controllers;

use application\core\Controller;
use application\lib\Pagination;
use application\models\Main;

class AdminController extends Controller {

	public function __construct($route){
		parent::__construct($route);
		$this->view->layout = 'admin';
	}

	public function loginAction() {
		if (isset($_SESSION['admin'])) {
			$this->view->redirect('admin/add');
		}
		if (!empty($_REQUEST)) {
			if (!$this->model->loginValidate($_REQUEST)) {
				$this->view->message('Error!', $this->model->error);
			} else {
				$_SESSION['admin'] = true;
				$this->view->location('admin/add');

			}
		}
		$this->view->render('Вход');
	}

	public function addAction() {
		if (!empty($_REQUEST)) {
			if (!$this->model->postValidate($_REQUEST, 'add')) {
				$this->view->message('Error!', $this->model->error);
			} 
			$id = $this->model->postAdd($_REQUEST); //добавляем пост
			if (!$id) { 
				$this->view->message('Error', 'Ошибка обработки запроса!');
			}
			$this->model->postUploadImage($_FILES['img']['tmp_name'], $id); //загружаем картинку к посту
			$this->view->message('success', 'Пост добавлен! \nid: '.$id);
			
		}
		$this->view->render('Добавить пост');
	}

	public function editAction() {
		$id = $this->route['id'];
		$res = $this->model->isPostExists($id);

		if (!$res) { 
			$this->view->errorCode(404); //Если нет поста с таким id, выводим 404
		}

		if (!empty($_REQUEST)) {
			if (!$this->model->postValidate($_REQUEST, 'edit')) {
				$this->view->message('Error!', $this->model->error);
			} else {
				$this->model->postEdit($_REQUEST, $id); //редактируем в базе (вызываем модель)
				if ($_FILES['img']['tmp_name']) { //Если прикреплено изображение, заменим в папке назначения
					$this->model->postUploadImage($_FILES['img']['tmp_name'], $id);
				}
					
				$this->view->message('success', 'Сохранено!');
			}
		}
		$vars = [
			'data' => $this->model->postData($id)[0],
		];

		$this->view->render('Изменить пост', $vars);
	}

	public function deleteAction() {
		$id = $this->route['id'];
		$res = $this->model->isPostExists($id);

		if (!$res) { 
			$this->view->errorCode(404); //Если нет поста с таким id, выводим 404
		}
		$this->model->postDelete($id); //Вызываем метод удаления (из модели)
		$this->view->redirect('admin/posts');
	}

	public function logoutAction() {
		unset($_SESSION['admin']);
		$this->view->redirect('admin/login');
	}

	public function postsAction() {
		$mainModel = new Main;
		$pagination = new Pagination($this->route, $mainModel->postsCount(), 10); //класс пагинатор. 1-й парам - номер страницы (через слеш на главной), второй - количество постов (100 = 10 страниц, Берем колличество из БД)

		$vars = [
			'pagination' => $pagination->get(),
			'list' => $mainModel->postsList($this->route),
		];
		$this->view->render('Список постов', $vars);
	}

	
}