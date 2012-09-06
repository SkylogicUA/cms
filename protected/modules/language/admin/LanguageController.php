<?php
/*
 * вывод каталога компаний и их данных llllllllllllllllll
 */

class LanguageController extends BaseController{

	protected $params;
	protected $db;

	function  __construct($registry, $params)
	{
		parent::__construct($registry, $params);
		$this->tb = "language";
        $this->name = "Языки";
		$this->registry = $registry;
	}

	public function indexAction()
	{
		$vars['message'] = '';
        $vars['name'] = $this->name; 
		if(isset($this->registry['access']))$vars['message'] = $this->registry['access'];
		/*if(isset($this->params['delete'])||isset($_POST['delete']))$vars['message'] = $this->delete();
		else*/
		if(isset($_POST['update']))$vars['message'] = $this->save();
		elseif(isset($_POST['update_close']))$vars['message'] = $this->save();
		//elseif(isset($_POST['add_close']))$vars['message'] = $this->add();

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

	private function add()
	{
		$message='';
		if(isset($_POST['comment']))
		{
            $param = array($_POST['comment']);
            $this->db->query("INSERT INTO `".$this->tb."` SET `comment`=?", $param);
			$message.= messageAdmin('Данные успешно добавлены');
		}
		//else $message.= messageAdmin('При добавление произошли ошибки', 'error');	
		return $message;
	}

	 
	private function save()
	{
		$message='';
		if(isset($this->registry['access']))$message = $this->registry['access'];
		else{
			if(isset($_POST['save_id'])&&is_array($_POST['save_id']))
			{
				if(isset($_POST['save_id'], $_POST['comment']))
				{
					for($i=0; $i<=count($_POST['save_id']) - 1; $i++)
					{
						$param = array($_POST['comment'][$i], $_POST['save_id'][$i]);
                        $this->db->query("UPDATE `".$this->tb."` SET `comment`=? WHERE id=?", $param);
					}
					$message .= messageAdmin('Данные успешно сохранены');
					if(isset($_POST['base']))$this->db->query("UPDATE `".$this->tb."` SET base=? WHERE id=?", array(1, $_POST['base']));
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
		else{
			 if(isset($this->params['delete'])&& $this->params['delete']>0)
			 {
				$id = $this->params['delete'];
				$key=$this->db->cell("Select `language`  FROM `".$this->tb."` WHERE `id`=?", array($id)) ;
			 
				$tables=$this->db->rows('show tables');
				$mass =array();
				sort($tables);
			 	$settings = Registry::set('db_settings', $const);
				$my_value = $key; 
				$array=$tables;
				$filtered_array = array_filter($array, function ($element) use ($my_value) 
											  { $mm =explode('_',$element["Tables_in_football"]);
												return ($mm[0] == $my_value);
											  }); 
				
				 
				foreach($filtered_array as $ky=>$val)
				{
					 $this->db->query("DROP TABLE `".$val["Tables_in_{$settings['name']}"]."`  " );
				}	
				if($this->db->query("DELETE FROM `".$this->tb."` WHERE `id`=?", array($id)))$message = messageAdmin('Запись успешно удалена');
			}
		}
		return $message;
	}


	private function listView()
	{
		$vars['list'] = $this->db->rows("SELECT 

											tb.*

										 FROM ".$this->tb." tb

											ORDER BY tb.`sort` ASC");

		return $vars;
	}
}
?>