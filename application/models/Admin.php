<?php

namespace application\models;

use application\core\Model;
use Imagick;


class Admin extends Model {
	
	public $error;

	public function loginValidate($post) { //Валидация логина/пароля
		$config = require_once 'application/config/admin.php';
		if ($config['login'] != $post['login'] || $config['password'] != $post['password']) {
			$this->error = "Некорректный логин или пароль!";
			return false;
		}
		return true;
	}

	public function postValidate($post, $type) { //Валидация формы добавления
		
		$nameLen = iconv_strlen($post['name']); //Возвращает количество символов в строке
		$descriptionLen = iconv_strlen($post['description']);
		$textLen = iconv_strlen($post['text']); 
		
		if ($nameLen < 3 || $nameLen > 100) {
			$this->error = 'Название должно содержать от 3 до 100 символов';
			return false;
		} elseif ($descriptionLen < 1 || $descriptionLen > 100) {
			$this->error = 'Описание должно содержать от 1 до 100 символов';
			return false;
		} elseif ($textLen < 1 || $textLen > 5000) {
			$this->error = 'Текст должен содержать от 1 до 5000 символов';
			return false;
		}
		/*
		if (empty($_FILES['img']['tmp_name']) && $type == 'add') {
				$this->error = 'Изображение не выбрано!';
				return false;
			}

		if ($type == 'edit' && $_FILES['img']['tmp_name']) {
			
		}*/

		return true;
	}

	public function postAdd($post) { //Добавление поста
		$params = [
			'id' => '',
			'name' => $post['name'],
			'description' => $post['description'],
			'text' => $post['text'],
		];


		/*$sql = 'INSERT INTO posts VALUES (:id, :name, :description, :text)';
		$this->db->query($sql, $params);*/ //для работы с классом Db я не использую

		//Для работы с классом SafeMySQl
		$sql = "INSERT INTO posts (name, description, ptext) VALUES ('".$post['name']."','". $post['description']."' , '".$post['text']."')";
		$this->db->query($sql);

		return $this->db->insertId();
	}

	public function postUploadImage($path, $id){ //загрузка картинок
		/*$img = new Imagick($path); //нужно устанавливать и подключать класс Imagick
		$img->cropThumbnailImage(1280, 720); //Ужимаем картинку до разрешения hd 720
		$img->setImageCompressionQuality(80); //ставим качество картинки 80% (сжимаем)
		$img->writeImage('public/materials/'.$id.'.jpg'); //Перемещаем картинку в директорию public/materials/ с сжатием*/
		
		move_uploaded_file($path, 'public/materials/'.$id.'.jpg'); //Перемещаем картинку в директорию public/materials/ без сжатия
	}

	public function isPostExists($id){ //Проверка существования поста
		$sql = "SELECT id FROM posts WHERE id = '".$id."'";
		return $this->db->getOne($sql); //false - если id не найден, рез-тат запроса - если найден
	}

	public function postDelete($id){ //Удаление поста
		$sql = "DELETE FROM posts WHERE id = '".$id."'";
		$res = $this->db->query($sql);
		unlink('public/materials/'.$id.'.jpg'); //удаляем картинку к посту
	}

	public function postData($id){
		$sql = "SELECT * FROM posts WHERE id = '".$id."'";
		return $this->db->getAll($sql); //Получаем ассоциативный массив из значений в БД
	}

	public function postEdit($post, $id){
		$sql = "UPDATE posts SET name = '".$post['name']."', description = '".$post['description']."', ptext = '".$post['text']."' WHERE id = '".$id."'";
		return $this->db->query($sql); //Редактируем запись в БД
	}

	/*public function postList($id){
		$sql = "SELECT * FROM posts";
		return $this->db->getAll($sql); //Получаем массив записей из БД
	}*/

}