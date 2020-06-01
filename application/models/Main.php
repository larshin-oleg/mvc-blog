<?php

namespace application\models;

use application\core\Model;

class Main extends Model {
	
	public $error;

	public function contactValidate($post) {
		$nameLen = iconv_strlen($post['name']); //Возвращает количество символов в строке
		$textLen = iconv_strlen($post['text']); //Возвращает количество символов в строке
		
		if ($nameLen < 1 || $nameLen > 30) {
			$this->error = 'Имя должно содержать от 1 до 30 символов';
			return false;
		} elseif (!filter_var($post['email'], FILTER_VALIDATE_EMAIL)) { //проверка email
			$this->error = 'Некорректный e-mail';
			return false;
		} elseif ($textLen < 1 || $textLen > 1000) {
			$this->error = 'Сообщение должно содержать от 1 до 1000 символов';
			return false;
		}
		return true;
	}

	public function postsCount(){
		$sql = "SELECT COUNT(id) FROM posts";
		return $this->db->getOne($sql);
	}

	public function postsList($route){
		//Смещение выборки по базе (постранично)
		$max = 10; //Диапазон постов на странице
		$start  = (($route['page'] ?? 1) - 1) * $max; //С какого эл-та стартует выборка
		//Запрос:
		$sql = 'SELECT * FROM posts ORDER BY id DESC LIMIT '.$start.', '.$max;
		return $this->db->getAll($sql);
	}
}