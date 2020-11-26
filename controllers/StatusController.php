<?php

class StatusController extends Controller {
	
	protected $auth_actions = array('index','post');

	//投稿データを扱う
	public function indexAction() {

		//セッションからuserデータを取得
		$user = $this->session->get('user');
		//userのidを元に投稿データを取得
		$statuses = $this->db_manager->get('Status')->fetchAllPersonalArchivesByUserId($user['id']);

		return $this->render(array(
			'statuses' => $statuses,
			'body' => '',
			'_token' => $this->generateCsrfToken('status/post'),
		));
	}

	//データ投稿処理
	public function postAction() {

		//actionがpostか確認
		if(!$this->request->isPost()) {
			$this->forward404();
		}

		//CSRFチェック
		$token = $this->request->getPost('_token');
		if(!$this->checkCsrfToken('status/post', $token)){
			return $this->redirect('/');
		}

		//投稿内容を取得
		$body = $this->request->getPost('body');

		$errors = array();

		//投稿内容のバリデーション
		if(!strlen($body)){
			$errors[] = 'ひとことを入力してください';
		} else if(mb_strlen($body) > 200){
			$errors[] = 'ひとことは200文字以内で入力してください';
		}

		//statusテーブルにuserのidと投稿を保存しトップ画面へリダイレクト
		if(count($errors) === 0){
			$user = $this->session->get('user');
			$this->db_manager->get('Status')->insert($user['id'], $body);

			return $this->redirect('/');
		}

		//エラーが発生した場合投稿一覧を取得し入力画面へ
		$user = $this->session->get('user');
		$statuses = $this->db_manager->get('Status')
			->fetchAllPersonalArchivesByUserId($user['id']);

		return $this->render(array(
			'errors'	=> $errors,
			'body'	=> $body,
			'statuses' => $statuses,
			'_token'   => $this->generateCsrfToken('status/post'),
		), 'index');
	}

	//ユーザーの投稿一覧
	public function userAction($params) {

		//ユーザーが存在しているか確認
		$user = $this->db_manager->get('User')
			->fetchByUserName($params['user_name']);
		if (!$user) {
			$this->forward404();
		}

		//ユーザーの投稿一覧を取得
		$statuses = $this->db_manager->get('Status')
			->fetchAllByUserId($user['id']);

		//フォロー状態を格納
		$following = null;

		//ログイン状態
		if ($this->session->isAuthenticated()) {
			//sessionからuserデータを取得
			$my = $this->session->get('user');
			//アクセスしている先の投稿が自身の投稿かどうかを確認
			if ($my['id'] !== $user['id']) {
				//フォローしているか確認
				$following = $this->db_manager->get('Following')
					->isFollowing($my['id'], $user['id']);
			}
		}	
		return $this->render(array(
			'user'		=> $user,
			'statuses' 	=> $statuses,
			'following' => $following,
			'_token' 	=> $this->generateCsrfToken('account/follow'),
		));
	}

	//投稿の詳細
	public function showAction($params) {

		//投稿id、ユーザーidを元にレコードを取得
		$status = $this->db_manager->get('Status')
			->fetchByIdAndUserName($params['id'], $params['user_name']);

		if(!$status) {
			$this->forward404();
		}

		return $this->render(array('status' => $status));
	}
}
?>