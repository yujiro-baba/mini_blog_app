<?php

class StatusController extends Controller {
	
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
}
?>