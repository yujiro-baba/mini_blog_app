<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" media="screen" href="/css/style.css"/>
	<!-- $titleを指定していればタイトルとして表示 -->
	<title><?php if (isset($title)): echo $this->escape($title) . ' - ';
		endif; ?>Mini Blog</title>
</head>
<body>
	<div id="header">
		<h1><a href="<?php echo $base_url; ?>/">Mini Blog</a></h1>
	</div>

	<div id="nav">
		<p>
			<?php if($session->isAuthenticated()): ?>
			<a href="<?php echo $base_url; ?>/">ホーム</a>
			<a href="<?php echo $base_url; ?>/account">アカウント</a>
			<?php else: ?>
			<a href="<?php echo $base_url; ?>/account/signin">ログイン</a>
			<a href="<?php echo $base_url; ?>/account/signup">アカウント登録</a>
			<?php endif; ?>
		</p>
	</div>

	<div id="main">
		<!--　アクションごとのHTMLを格納 -->
		<?php echo $_content; ?>
	</div>
</body>
</html>	