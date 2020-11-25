<?php 

class AccountController extends Controller {

	//登録画面表示
	public function signupAction() {

		if($this->session->isAuthenticated()){
			return $this->redirect('/account');
		}
		
		return $this->render(array(
			'user_name' => '',
			'password' => '',
			'_token' => $this->generateCsrfToken('account/signup'),
		));
	}

	//ユーザー登録処理
	public function registerAction() {

		if($this->session->isAuthenticated()){
			return $this->redirect('/account');
		}

		//リクエストがPOSTかどうか判定
		if(!$this->request->isPost()) {
			$this->forward404();
		}

		//トークンを取得し整合性をチェック
		$token = $this->request->getPost('_token');
		if($this->checkCsrfToken('account/signup', $token)) {
			return $this->redirect('/account/signup');
		}

		$user_name = $this->request->getPost('user_name');
		$password = $this->request->getPost('password');

		$errors = array();

		//ユーザーIDバリデーション
		if (!strlen($user_name)) {
			$errors[] = 'ユーザIDを入力してください';
		} else if (!preg_match('/^\w{3,20}$/', $user_name)) {
			$errors[] = 'ユーザIDは半角英数字およびアンダースコアを3 ～ 20 文字以内で入力してください';
		} else if (!$this->db_manager->get('User')->isUniqueUserName($user_name)) {
			$errors[] = 'ユーザIDは既に使用されています';
		}

		//パスワードバリデーション
		if(!strlen($password)) {
			$errors[] = 'パスワードを入力してください';
		} else if (4 > strlen($password) || strlen($password) > 30) {
			$errors[] = 'パスワードは4~30文字以内で入力してください';
		}

		//エラーがなければデータを登録しトップ画面へリダイレクト
		if(count($errors) === 0) {
			$this->db_manager->get('User')->insert($user_name, $password);
			$this->session->setAuthenticated(true);

			$user = $this->db_manager->get('User')->fetchByUserName($user_name);
			$this->session->set('user', $user);

			return $this->redirect('/');
		}

		//エラーがある場合は登録画面を再度レンダリング
		return $this->render(array(
			'user_name' => $user_name,
			'password' => $password,
			'errors' => $errors,
			'_token' => $this->generateCsrfToken('account/signup'),
		),'signup');
	}

	public function indexAction() {
		$user = $this->session->get('user');

		return $this->render(array('user' => $user));
	}

	//ログイン画面
	public function sigininAction() {

		if($this->session->isAuthenticated()){
			return $this->redirect('/account');
		}

		return $this->render(array(
			'user_name' => '',
			'password' => '',
			'_token' => $this->generateCsrfToken('account/signin'),
		));

	}

	//ログイン処理
	public function authenticateAction(){

		if ($this->session->isAuthenticated()) {
			return $this->redirect('/account');
		}

		if (!$this->request->isPost()) {
			$this->forward404();
		}

		$token = $this->request->getPost('_token');
		if (!$this->checkCsrfToken('account/signin', $token)) {
			return $this->redirect('/account/signin');
		}

		//post情報からユーザー名、パスワードを取得
		$user_name = $this->request->getPost('user_name');
		$password = $this->request->getPost('password');

		$errors = array();

		//バリデーション
		if (!strlen($user_name)) {
			$errors[] = 'ユーザIDを入力してください';
		}

		if (!strlen($password)) {
			$errors[] = 'パスワードを入力してください';
		}

		//ユーザーが存在するか確認
		if (count($errors) === 0) {
			$user_repository = $this->db_manager->get('User');
			$user = $user_repository->fetchByUserName($user_name);

			//バリデーションに通ればHPにリダイレクト、エラーがあれば入力画面に戻る
			if (!$user
				|| ($user['password'] !== $user_repository->hashPassword($password))
			) {
				$errors[] = 'ユーザIDかパスワードが不正です';
			} else {
				$this->session->setAuthenticated(true);
				$this->session->set('user', $user);

				return $this->redirect('/');
			}
		}

		return $this->render(array(
			'user_name' => $user_name,
			'password'	=> $password,
			'errors'	=> $errors,
			'_token'	=> $this->generateCsrfToken('account/signin'),
		), 'signin');
	}

	//ログアウト処理
	public function signoutAction() {

		//セッション情報を削除
		$this->session->clear();
		$this->session->setAuthenticated(false);

		//ログイン画面にリダイレクト
		return $this->redirect('/account/signin');
	}


}


?>