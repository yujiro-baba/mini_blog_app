<?php

class FollowingRepository extends DbRepository {

	//followingテーブルにレコード追加
	public function insert($user_id, $following_id) {

		$sql = "
			INSERT INTO following VALUES(:user_id, :following_id)
		";

		//ログインユーザーを$user_id、フォロー対象の$following_idとして指定
		$stmt = $this->execute($sql,array(
			':user_id' => $user_id,
			':follwoing_id' => $following_id,
		));

	}

	//フォローチェック
	public function isFollowing($user_id, $following_id) {

		//followingテーブルから$user_idと$following_idの一致するレコードを返す
		$sql = "
			SELECT COUNT(user_id) as count
				FROM following
				WHERE user_id = :user_id
					AND following_id = :following_id
		";

		$row = $this->fetch($sql, array(
			':user_id' => $user_id,
			':following_id' => $following_id,
		));

		if($row['count'] !== '0') {
			return true;
		}

		return false;

	}



}


?>