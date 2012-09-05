<?php
/*
 * вывод каталога компаний и их данных
 */
class MetaController extends BaseController{
	
	protected $params;
	protected $db;
	
	function  __construct($registry, $params)
	{
		$this->tb = "meta_data";
		$this->name = "Мета-данные";
		$this->registry = $registry;
		//$this->db->row("SELECT FROM `moderators_permission` WHERE `id`=?", array($_SESSION['admin']['id']));
		parent::__construct($registry, $params);
	}

	public function indexAction()
	{
		$vars['message'] = '';
		$vars['name'] = $this->name;
		if(isset($this->registry['access']))$vars['message'] = $this->registry['access'];
		if(isset($this->params['delete'])||isset($_POST['delete']))$vars['message'] = $this->delete();
		elseif(isset($_POST['update']))$vars['message'] = $this->save();
		elseif(isset($_POST['update_close']))$vars['message'] = $this->save();
		elseif(isset($_POST['add_close']))$vars['message'] = $this->add();
		
		$view = new View($this->registry);
		$vars['list'] = $view->Render('view.phtml', $this->listView());
		$data['content'] = $view->Render('list.phtml', $vars);
		return $this->Render($data);
	}
	
	public function addAction()
	{
		$vars['message'] = '';
		if(isset($_POST['add']))$vars['message'] = $this->add();
		
		$vars['list'] = $this->listView();
		$view = new View($this->registry);
		$data['content'] = $view->Render('add.phtml', $vars);
		return $this->Render($data);
	}
	
	public function editAction()
	{
		//if($vars['message']!='')return Router::act('error');
		$vars['message'] = '';
		if(isset($_POST['update']))$vars['message'] = $this->save();
		
		$vars['edit'] = $this->db->row("SELECT 
											tb.*
										FROM ".$this->tb." tb
										WHERE
											tb.id=?",
										array($this->params['edit']));
		if(isset($this->params['duplicate']))$vars['message'] = $this->duplicate($vars['edit']);								
		$vars['list'] = $this->listView();
		$view = new View($this->registry);
		$data['content'] = $view->Render('edit.phtml', $vars);
		return $this->Render($data);
	}
	
	
	private function duplicate($row)
	{
		$message='';
		if(isset($this->registry['access']))$message = $this->registry['access'];
		else
		{
			$message='';
			if(isset($row['active'], $row['url'])&&$row['url']!="")
			{
                $url=$row['url'].'2';
				$param = array($url, $row['title'], $row['keywords'], $row['description'], $row['body'], $row['active']);
				$insert_id = $this->db->insert_id("INSERT INTO `".$this->tb."` SET `url`=?, `title`=?, `keywords`=?, `description`=?, `body`=?, `active`=?", $param);
				header("Location: /admin/meta/edit/".$insert_id);
				//$message.= messageAdmin('Данные успешно клонированы');
			}
			//else $message.= messageAdmin('При добавление произошли ошибки', 'error');	
		}
		return $message;
	}
	
	private function add()
	{
		$message='';
		if(isset($_POST['active'], $_POST['url'], $_POST['title'], $_POST['keywords'], $_POST['description'], $_POST['body'])&&$_POST['url']!="")
		{
			if($_POST['url']=='')$url = translit($_POST['name']);
			else $url = translit($_POST['url']);

			$param = array($_POST['title'], $_POST['keywords'], $_POST['description'], $_POST['body'], $_POST['active']);
			$insert_id = $this->db->insert_id("INSERT INTO `".$this->tb."` SET `title`=?, `keywords`=?, `description`=?, `body`=?, `active`=?", $param);
			$message = $this->checkUrl($this->tb, $url, $insert_id);
			$message.= messageAdmin('Данные успешно добавлены');
		}
		//else $message.= messageAdmin('При добавление произошли ошибки', 'error');	
		return $message;
	}
	
	
	private function save()
	{
		$message='';
		if(isset($this->registry['access']))$message = $this->registry['access'];
		else
		{
			if(isset($_POST['save_id'])&&is_array($_POST['save_id']))
			{
				if(isset($_POST['save_id'], $_POST['name'], $_POST['url']))
				{
					for($i=0; $i<=count($_POST['save_id']) - 1; $i++)
					{
						if($_POST['url'][$i]=='')$url = translit($_POST['name'][$i]);
						else $url = $_POST['url'][$i];
						//echo $_POST['name'][$i].'<br>';
						$message = $this->checkUrl($this->tb, $url, $_POST['save_id'][$i]);
						$param = array($_POST['name'][$i], $_POST['save_id'][$i]);
						$this->db->query("UPDATE `".$this->tb_lang."` SET `name`=? WHERE ".$this->tb."_id=?", $param);
					}
					$message .= messageAdmin('Данные успешно сохранены');
				}
				else $message .= messageAdmin('При сохранение произошли ошибки', 'error');
			}
			else{
				if(isset($_POST['active'], $_POST['url'], $_POST['id'], $_POST['title'], $_POST['keywords'], $_POST['description'], $_POST['body']))
				{
					$param = array($_POST['url'], $_POST['title'], $_POST['keywords'], $_POST['description'], $_POST['body'], $_POST['active'], $_POST['id']);
					$this->db->query("UPDATE `".$this->tb."` SET `url`=?, `title`=?, `keywords`=?, `description`=?, `body`=?, `active`=? WHERE id=?", $param);

					$message .= messageAdmin('Данные успешно сохранены');
				}
				else $message .= messageAdmin('При сохранение произошли ошибки', 'error');
			}
		}
		return $message;
	}
	
	private function delete()
	{
		$message='';
		if(isset($this->registry['access']))$message = $this->registry['access'];
		else
		{
			if(isset($_POST['id'])&&is_array($_POST['id']))
			{
				for($i=0; $i<=count($_POST['id']) - 1; $i++)
				{
					$this->db->query("DELETE FROM `".$this->tb."` WHERE `id`=?", array($_POST['id'][$i]));
				}
				$message = messageAdmin('Запись успешно удалена');
			}
			elseif(isset($this->params['delete'])&& $this->params['delete']!='')
			{
				$id = $this->params['delete'];
				if($this->db->query("DELETE FROM `".$this->tb."` WHERE `id`=?", array($id)))$message = messageAdmin('Запись успешно удалена');
			}
		}
		return $message;
	}
	
	private function listView($WHERE='')
	{
		$vars['list'] = $this->db->rows("SELECT 
											tb.*
										 FROM ".$this->tb." tb
										 $WHERE
										 ORDER BY tb.`id` DESC");
		return $vars;
	}
}
?>