<?php

$arrContextOptions = [
    "ssl" => [
        "verify_peer" => false,
        "verify_peer_name" => false,
    ],
];

function timeAgo($dateString)
{
    if (empty($dateString)) {
        return "";
    }

    try {
        $date = new DateTime($dateString);
        $currentTime = new DateTime();

        $interval = $currentTime->diff($date);

        if ($interval->y >= 1) {
            return $interval->y . " y";
        } elseif ($interval->m >= 1) {
            return floor($interval->days / 7) . " w";
        } elseif ($interval->d >= 1) {
            return $interval->d . " d";
        } elseif ($interval->h >= 1) {
            return $interval->h . " h";
        } elseif ($interval->i >= 1) {
            return $interval->i . " m";
        } else {
            return "0m";
        }
    } catch (Exception $e) {
        return "";
    }
}

if (isset($_GET['username'])) {
    $username = $_GET['username'];

    $json = file_get_contents('https://webapi.highrise.game/users?&username=' . $username . '&sort_order=asc&limit=1', false, stream_context_create($arrContextOptions));

    $data1 = json_decode($json, true);

    if (!isset($data1["users"][0]["user_id"])) {
        echo "User not found";
        exit();
    }
    $user_id = $data1["users"][0]["user_id"];
} elseif (isset($_GET['id'])) {
    $user_id = $_GET['id'];
} else {
    echo "No user specified";
    exit();
}

($json = file_get_contents('https://webapi.highrise.game/users/' . $user_id . '', false, stream_context_create($arrContextOptions))) or die('Error occured');

($data2 = json_decode($json, true)) or die('Invalid user id');

$userdata = $data2["user"];

if (isset($_GET['starts_after'])) {
    $starts_after = "&starts_after=" . $_GET['starts_after'];
} else {
    $starts_after = "";
}

if (isset($_GET['ends_before'])) {
    $ends_before = "&ends_before=" . $_GET['ends_before'];
} else {
    $ends_before = "";
}

$sort_order = "desc";

if (isset($_GET['sort_order'])) {
    $sort_order = $_GET['sort_order'];
}

$json = file_get_contents('https://webapi.highrise.game/posts?limit=5' . $starts_after . $ends_before . '&sort_order=' . $sort_order . '&author_id=' . $user_id . '', false, stream_context_create($arrContextOptions));

$data3 = json_decode($json, true);

$posts = $data3["posts"];
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" >
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="https://unpkg.com/bootstrap@4.5.0/dist/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://unpkg.com/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
		<script src="https://code.jquery.com/jquery-3.1.0.min.js" integrity="sha256-cCueBR6CsyA4/9szpPfrX3s49M9vUU5BgtiJj06wt/s=" crossorigin="anonymous"></script>
		<style> 


		
body {
	background-color: #191919;
}

#user-name, #user-bio {
	position: relative;
	z-index: 3;
}

#user-bio {
	white-space: pre-wrap
}

html * {
	font-family: "Exo", sans-serif;
}

h1, h2, h3, h4, h5, h1 span, h2 span, pre{
	font-family: "Exo", sans-serif;
}

nav {
	background-color: transparent;
	top: 0;
	width: 100%;
	z-index: 100;
	height: 70px;
}

.navbar-toggler {
	border: none;
	font-size: 200% !important;
}

.bg-light {
	background-color: #FBFBFB !important;
}

#site-logo {
	height: 50px;
}

.box-shadow {
	box-shadow: 0px 0px #888;
}
.rounded-container, .rounded-corners img {
	border-radius: 8px !important;
}

.card-body {
	border-radius: 8px !important;
}
.rounded-card {
	border-radius: 8px !important;
}

@media (max-width: 767px) {
	
	nav {
		min-height: 60px !important;
    }
	
	.navbar-collapse {
		text-align: left;
		background-color: #F7F7F7;
		padding: 2%;
		position: absolute;
		top: 60px !important;
		left: 0;
		width: 100% !important;
	}
	
	#site-logo {
		height: 41px;
	}
	
	.navbar-collapse li a {
		color: #101010 !important;
    }
	
	.post-author {
		font-soze: 80%;
	}
	
}

.jumbotron {
	padding-bottom: 77px;
	border-radius: 0;
	overflow: hidden;
	width: 100% !important;
	background-color: #151515;
}

.jumbotron p {
	position: relative;
	z-index: 20;
}

.purple {
	color:#B593EE;
}

.text-light p, #logo {
	color: #B7B7B7 !important;
}

#post-text {
	white-space: pre-wrap;
}

.post-author {
	color: #B593EE;
}

</style>
<title>Website</title>
	</head>
	<body>
		<nav class="navbar navbar-expand-md position-absolute navbar-dark">
			<div class="container-fluid">
				<a href="index.php" id="logo">LOGO</a>
			</div>
		</nav>
				<section class="jumbotron mb-0 text-light text-left" style="padding-top:96px;position:relative;margin-top:0px;">
			<h2 id="user-name"><?php echo $userdata["username"]; ?></h2>
			<p id="user-bio" class="lead"><?php echo $userdata["bio"]; ?></p>
			
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-3">
					<p><i class="bi-clock purple"></i> Joined on <strong><?php echo substr($userdata["joined_at"], 0, 10); ?></strong></p>
					</div>
					<div class="col-md-3">
					<p><i class="bi-person-fill purple"></i> <strong><?php echo $userdata["num_following"]; ?></strong> following</p>
					</div>
							<div class="col-md-3">
								<p><i class="bi-person-fill purple"></i> <strong><?php echo $userdata["num_followers"]; ?></strong> followers</p>

					</div>
					<div class="col-md-3">
								<p><i class="bi-person-fill purple"></i> <strong><?php echo $userdata["num_friends"]; ?></strong> friends</p>

					</div>
			
				</div>
			</div>	
		</section>
		<div class="container-fluid pt-3 pb-2 text-left text-light">
		
<div class="row px-4">
		<div class="col-6 py-3 text-left text-light">
				<h2 style="color:#B593EE;">Posts</h2>
		</div>
		<div class="col-6 py-3 text-right">
		<?php if ($sort_order == "asc") { ?>
			<h4><a href="profile.php?id=<?php echo $user_id; ?>&sort_order=desc" class="text-muted"><i class="bi-sort-down"></i></a></h4>
			<?php } else { ?>
				<h4><a href="profile.php?id=<?php echo $user_id; ?>&sort_order=asc" class="text-muted"><i class="bi-sort-up"></i></a></h4>
		<?php } ?>
		</div>
		</div>
			<div class="container-fluid">
				<div class="posts">
<?php
$s_a = "";
$e_b = "";
for ($i = 0; $i < 5; $i++) {
    $post_content = "";
    if (isset($posts[$i])) {

        $s_a = $posts[$i]['post_id'];
        if ($i == 0) {
            $e_b = $posts[$i]['post_id'];
        }

		$post_id = $posts[$i]['post_id'];
        if ($posts[$i]['type'] == "photo") {
            $post_text = $posts[$i]['caption'];
            $post_image = $posts[$i]['file_key'];
            $post_content = <<<HTML
<h4 class="my-4 post-text">$post_text</h4>
<a href="post.php?id=$post_id">
<img src="https://d4v5j9dz6t9fz.cloudfront.net/$post_image" alt="$post_text" style="display:block;max-width:100%;" class="mb-4">
</a>
HTML;
        } elseif ($posts[$i]['type'] == "video") {
            $post_text = $posts[$i]['caption'];
            $post_video = $posts[$i]['file_key'];
            $post_content = <<<HTML
<h4 class="my-4 post-text">$post_text</h4>
<video style="display:block;max-width:100%;" class="mb-4" controls>
  <source src="src="https://d4v5j9dz6t9fz.cloudfront.net/$post_video">
  Your browser does not support the video tag.
</video>	
HTML;
        } elseif ($posts[$i]['type'] == "text") {
            $post_text = $posts[$i]['body']['text'];
            $post_content = <<<HTML
<h4 class="my-4 post-text">$post_text</h4>
	
HTML;
        }
        ?>
	<div class="box-shadow rounded-card mb-4">
	
	<div class="card-body bg-dark text-light">
	
<div class="row">
	
	<div class="col-6">	
	<h2 class="post-author"><?php echo $userdata["username"]; ?></h2>
		
	</div>
	
	<div class="col-6 text-right">	
		<h4 title="<?php echo substr($posts[$i]['created_at'], 0, 10); ?>">
			<a href="post.php?id=<?php echo $posts[$i]['post_id']; ?>" class="text-muted">
				<?php echo timeAgo($posts[$i]['created_at']); ?>
			</a>
		</h4>	
	</div>

</div>	
	
	
<?php echo $post_content; ?>


<br>	

	<h4 id="likes-and-comments"><i class="bi-heart purple"></i> <?php echo $posts[$i]['num_likes']; ?>
	&nbsp;
	<a href="post.php?id=<?php echo $posts[$i]['post_id']; ?>" class="text-light"><i class="bi-chat purple"></i> <?php echo $posts[$i]['num_comments']; ?></a></h4>
	


	</div>
	</div>
<?php
    }
}
?>
						
					
					
<div class="text-center">
<h3><?php if (isset($_GET['starts_after'])) { ?><a class="text-muted mr-4" href="profile.php?id=<?php echo $user_id; ?>&sort_order=<?php echo $sort_order; ?>&ends_before=<?php echo $e_b; ?>">&laquo; Previous</a><?php } ?> 
<a class="text-muted" href="profile.php?id=<?php echo $user_id; ?>&sort_order=<?php echo $sort_order; ?>&starts_after=<?php echo $s_a; ?>">Next &raquo;</a> </h3>
</div>
					
				</div>
			</div>
		</div>


		<footer class="py-5">
			<div class="container">
				<div class="row">
					<div class="col-md-12 text-center">
						<p class="text-dark mb-0">Coded by @peanutbag using highrise Webapi</p>
					</div>
				</div>
			</div>
		</footer>
	</body>
</html>	
